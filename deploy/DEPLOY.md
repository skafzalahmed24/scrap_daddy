# Scrap Daddy - server deploy (shared hosting / cPanel / Hostinger)

Build the package with `php deploy/build-production.php` (creates `../scrap_daddy_production.zip`).

## Layout after upload
```
/home/USER/
  scrap_daddy_core/   <- Laravel app (private, NOT web accessible)
  public_html/        <- web root (index.php + .htaccess + images + css)
```
`public_html/index.php` already finds `../scrap_daddy_core`. The source of these files is `public/` in this repo.

## Steps
1. Upload the zip to `/home/USER/` and Extract (File Manager). Turn on "Show Hidden Files" to see `.htaccess`.
2. In `scrap_daddy_core/` rename `.env.production` -> `.env` and fill:
   `APP_URL`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, mail and Razorpay keys.
   If the site already has live data, paste your EXISTING `APP_KEY` instead of the generated one.
3. Migrate: `cd ~/scrap_daddy_core && php artisan migrate --force`
   (no SSH: import the database via phpMyAdmin, then run `2026_11_11_100000_create_service_pincodes_table.php` once).
4. Permissions 775: `scrap_daddy_core/storage`, `scrap_daddy_core/bootstrap/cache`,
   and `public_html/uploads, profiles, categories, subcategories`.
5. PHP 8.2 with: pdo_mysql, mbstring, openssl, fileinfo, gd, curl, zip.
6. Optional (SSH): `php artisan config:cache && php artisan route:cache && php artisan view:cache`
   (re-run after every .env change).
7. Admin > Service Areas: add the PIN codes you serve.
8. Admin > Payment Settings: set advance payment (on/off + %), and enable Razorpay / UPI / bank / cash.
   Razorpay needs `RAZORPAY_KEY` and `RAZORPAY_SECRET` in `.env` (the app and website now take the key from the server).

## Checks
- `https://your-domain/.env` must show 403/404.
- Login, OTP, place a pickup, upload an image.
- `scrap_daddy_core/storage/logs/` for errors.

## Updating later
Upload changed files only; never overwrite `.env`, `storage/` or the upload folders in `public_html`.
