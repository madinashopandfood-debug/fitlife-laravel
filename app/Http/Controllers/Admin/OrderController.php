<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Paginated, searchable, filterable order list.
     * Pagination keeps this from ever loading thousands of rows at once.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'newest');

        $orders = Order::query()
            ->search($request->get('q'))
            ->status($request->get('status'))
            ->dateRange($request->get('from'), $request->get('to'))
            ->when($sort === 'oldest', fn ($q) => $q->oldest())
            ->when($sort !== 'oldest', fn ($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'filters' => $request->only(['q', 'status', 'from', 'to', 'sort']),
        ]);
    }

    public function show(Order $order)
    {
        $logs = ActivityLog::where('order_id', $order->id)->latest()->get();
        return view('admin.orders.show', compact('order', 'logs'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', ['order' => $order, 'statuses' => Order::STATUSES]);
    }

    /**
     * Moderators may edit customer-facing fields + status, but the
     * "can delete" gate below still applies for the delete action itself.
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:' . implode(',', Order::STATUSES)],
        ]);

        $statusChanged = $data['status'] !== $order->status;
        $order->update($data);

        ActivityLog::record(Auth::id(), 'order.updated', $order->id, 'Order details updated');
        if ($statusChanged) {
            ActivityLog::record(Auth::id(), 'order.status_changed', $order->id, "Status changed to {$data['status']}");
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated.');
    }

    /**
     * Lightweight endpoint used by the quick status-change dropdown on the
     * order list, so moderators don't have to open the full edit form.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Order::STATUSES)],
        ]);

        $order->update(['status' => $data['status']]);
        ActivityLog::record(Auth::id(), 'order.status_changed', $order->id, "Status changed to {$data['status']}");

        return back()->with('success', 'Status updated.');
    }

    /**
     * Admin can always delete. Moderators can only delete if explicitly
     * granted (see policy check in the route/middleware layer).
     */
    public function destroy(Request $request, Order $order)
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Only admins can delete orders.');
        }

        ActivityLog::record($user->id, 'order.deleted', null, "Order {$order->order_code} deleted");
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    /**
     * CSV export (works for the current filtered result set).
     */
    public function exportCsv(Request $request)
    {
        $orders = Order::query()
            ->search($request->get('q'))
            ->status($request->get('status'))
            ->dateRange($request->get('from'), $request->get('to'))
            ->latest()
            ->get();

        $filename = 'orders_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order ID', 'Name', 'Phone', 'Address', 'Quantity', 'Note', 'Status', 'Created At']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_code,
                    $order->customer_name,
                    $order->phone,
                    $order->address,
                    $order->quantity,
                    $order->note,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
