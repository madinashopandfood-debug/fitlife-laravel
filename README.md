# Fit Life — Order Management System

A complete Laravel backend that replaces the old Google Sheets order flow for the Fit Life product page, with an admin panel, Telegram order notifications, and Meta Pixel/Conversions API (CAPI) tracking.

---

## 1. What's included

- **Customer-facing API** (`POST /api/orders`) — receives orders from `index.html`, saves them to the database, and (optionally) fires Telegram + Meta CAPI notifications.
- **Admin panel** (`/admin`) — login, dashboard, order management (search/filter/status/export), moderator (staff) management, and settings for Telegram / Meta Pixel / Meta CAPI.
- **Updated `index.html`** — same design as before, only the order-submission JavaScript was changed to call your new backend instead of Google Apps Script.
- Role-based access: **Admin** (full access) and **Moderator** (orders only, no settings/staff management).

---

## 2. Requirements

- PHP 8.2 or newer
- Composer
- MySQL or MariaDB (or SQLite for quick testing)
- A web server (Apache/Nginx) or shared hosting with PHP support
- Standard Laravel 11 requirements (extensions: mbstring, openssl, pdo, tokenizer, xml, ctype, json, bcmath, fileinfo)

---

## 3. Installation

### Step 1 — Upload the project

Upload the entire `fitlife-laravel` folder to your server (e.g. via FTP, cPanel File Manager, or `git clone` if you push this to a repository).

### Step 2 — Install dependencies

From inside the project folder, run:

```bash
composer install --optimize-autoloader --no-dev
```

### Step 3 — Configure your environment

Copy the example environment file:

```bash
cp .env.example .env
```

Open `.env` and set the following:

```env
APP_NAME="Fit Life"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Asia/Dhaka

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitlife
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Comma-separated list of origins allowed to call the public order API.
# This should be the domain(s) where index.html is hosted.
FRONTEND_ALLOWED_ORIGINS=https://your-storefront-domain.com
```

### Step 4 — Generate the application key

```bash
php artisan key:generate
```

This key is also used to encrypt your Telegram bot token and Meta CAPI access token in the database, so keep your `.env` file private and backed up.

### Step 5 — Run migrations and seed the admin account

```bash
php artisan migrate --seed
```

This creates all required tables and one initial admin account:

- **Email:** `madinashopandfood@gmail.com`
- **Temporary password:** `Admin@123`

You will be **required to change this password** the first time you log in.

### Step 6 — Storage and permissions

```bash
php artisan storage:link
```

Make sure the `storage/` and `bootstrap/cache/` folders are writable by the web server (`chmod -R 775 storage bootstrap/cache` on Linux hosting).

### Step 7 — Point your web server at `public/`

Your web server's document root should point to the Laravel `public/` folder, not the project root. On most shared hosts, this means:

- Either set the domain's document root directly to `public/`, **or**
- Move the contents of `public/` to your `public_html` and edit `index.php` to point to the correct `../vendor/autoload.php` and `../bootstrap/app.php` paths.

### Step 8 — Queue (optional)

Telegram and Meta CAPI calls run synchronously by default, which is fine for normal traffic. If you expect high order volume and want these to run in the background instead, set up a queue worker:

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work --daemon
```

(This step is optional — the system works correctly without it.)

---

## 4. First login & setup

1. Go to `https://your-domain.com/admin/login`.
2. Log in with the admin email and temporary password from Step 5.
3. You'll be prompted to set a new password immediately.
4. From the sidebar, go to **Settings** and configure:
   - **General** — store name, currency, default order quantity, etc.
   - **Telegram** — paste your bot token and chat ID, then use **Test Connection** to confirm it works.
   - **Meta Pixel** — your public Pixel ID (safe to expose to the browser).
   - **Meta CAPI** — your server-side access token (never exposed publicly) and test event code if you want to verify events in Meta's Test Events tool.

All secrets (Telegram bot token, Meta CAPI access token) are encrypted before being stored in the database.

---

## 5. Connecting your storefront (`index.html`)

The included `index.html` (inside `public_html/`) has already been updated to:

- Submit orders to `POST /api/orders` on your new backend instead of the old Google Apps Script endpoint.
- Load Meta Pixel configuration dynamically from `GET /api/pixel-config` (so you never need to hardcode your Pixel ID in the HTML again).
- Fire the browser-side Meta Pixel `Purchase` event using the same `event_id` the server used for its CAPI `Purchase` event, so Meta can de-duplicate them correctly.

To go live:

1. Open `public_html/index.html` and find this line near the top of the script section:
   ```js
   const ORDER_API_ENDPOINT = "/api/orders";
   ```
2. If your storefront is hosted on a **different domain** than the Laravel backend, change this to the full URL, e.g.:
   ```js
   const ORDER_API_ENDPOINT = "https://api.your-domain.com/api/orders";
   ```
   and do the same for the `/api/pixel-config` call a few lines above it.
3. Make sure that domain is listed in `FRONTEND_ALLOWED_ORIGINS` in your `.env` file (Step 3 above) so CORS allows the request.
4. Upload `index.html` to wherever your storefront is currently hosted (replacing the old file). No other files or visual styling were changed.

---

## 6. Managing staff (Moderators)

As an Admin, go to **Settings → Moderators** to:

- Create new staff accounts (a temporary password is generated automatically and must be changed on first login).
- Toggle accounts active/inactive.
- Reset a moderator's password.
- Delete an account.

Moderators can view and manage orders but cannot access Settings or Moderator management, and cannot delete orders.

---

## 7. Orders

From **Orders**, staff can:

- Search by customer name, phone, or order code.
- Filter by status (Pending, Confirmed, Hold, Cancelled, Delivered) and date range.
- Quickly change an order's status inline.
- View full order details, including whether Telegram/Pixel/CAPI notifications succeeded.
- Copy phone number, address, or full order info with one click (useful for manual courier entry).
- Export the currently filtered list to CSV.
- (Admin only) Delete an order.

**Note:** if Telegram or Meta CAPI notifications fail for any reason (wrong token, network issue, etc.), the order is still saved successfully — customers are never blocked by a notification failure.

---

## 8. Troubleshooting

- **500 error after upload:** double-check `.env` is present, `APP_KEY` is set, and `storage/`/`bootstrap/cache` are writable.
- **Orders save but Telegram doesn't send:** go to Settings → Telegram and use **Test Connection**; check that the bot has been started (`/start`) in the target chat and the chat ID is correct.
- **Pixel events not showing in Meta Events Manager:** confirm your Pixel ID is correct in Settings → Meta Pixel, and that `/api/pixel-config` returns `"enabled": true` when visited directly in a browser.
- **CORS errors in the browser console:** make sure the exact origin (protocol + domain, no trailing slash) of your storefront is listed in `FRONTEND_ALLOWED_ORIGINS`.

---

## 9. Support

This system was custom-built for the Fit Life order flow. For further customization (new order fields, additional payment integrations, SMS notifications, etc.), keep this README and the codebase structure as a reference for future development work.
