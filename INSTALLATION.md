# Installation

1. Copy the project to:

```text
C:\xampp\htdocs\Help-Desk-App
```

2. Start Apache and MySQL in XAMPP.

3. Create/import the database:

```text
database/pust_helpdesk.sql
```

4. Check database settings:

```php
// includes/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pust_helpdesk');
define('DB_USER', 'root');
define('DB_PASS', '');
```

5. Check the app URL:

```php
// includes/config/app.php
define('APP_URL', 'http://localhost/Help-Desk-App');
```

6. Configure email for password reset:

```text
Copy includes/config/mail.local.php.example
to   includes/config/mail.local.php
```

Then enter real SMTP credentials.

7. Open the app:

```text
http://localhost/Help-Desk-App/
```

## Troubleshooting

- Database error: confirm MySQL is running and credentials are correct.
- Upload error: ensure `assets/uploads/` is writable.
- Password reset email not sending: check `includes/config/mail.local.php`.
- Page paths wrong: confirm `APP_URL` matches your local folder or domain.
