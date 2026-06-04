# ClinicDesk

PHP final project for a login-protected clinic management dashboard.

## Setup

1. Copy `config/database.example.php` to `config/database.php` and update credentials.
2. Import `database/schema.sql` into MySQL.
3. Copy AdminLTE 3 assets into `public/assets/adminlte/`.
4. Serve the project through Apache/PHP so `.htaccess` routing and upload access rules apply.

Default admin account:

- Email: `admin@clinic.local`
- Password: `Admin@1234`

## Notes

- `config/database.php` is intentionally ignored.
- Prescription PDFs are served only through the PHP download action.
- All POST forms use CSRF tokens.
