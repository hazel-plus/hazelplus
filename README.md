# Hazel+

A revival of Google+, built in a single PHP file.

I started this project because I genuinely missed Google+ and couldn't find anything that felt like it. Circles, Communities, +1s, Ripples — all of it. So I rebuilt it from scratch. It's not perfect but it works, and now it's open source so you can make it better than I can.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)
![Single file](https://img.shields.io/badge/single-file-blue)

---

## Features

- **Posts** — share text and images, control visibility (public or circles)
- **Circles** — drag-and-drop interface for organizing who you follow
- **Communities** — create and join topic-based groups with custom icons and banners
- **+1s** — the original reaction button
- **Reshares + Ripples** — reshare posts and visualize how they spread
- **Direct Messages** — private conversations between users
- **Notifications** — +1s, comments, follows, messages
- **Early Access Mode** — close registrations while you're getting started
- **Maintenance Mode** — take the site offline for everyone except admins
- **Suspension system** — suspend users temporarily or permanently with a reason
- **Admin panel** — user management, stats, settings

## Requirements

- PHP 7.4 or higher
- SQLite (default, no setup needed) or MySQL
- A web server (Apache, Nginx, Caddy, whatever)
- The `uploads/` directory needs to be writable

## Installation

1. Drop `index.php` into your web root (or anywhere really)
2. Open it in a browser — the database sets itself up on first run
3. Sign in with the default admin credentials and change them immediately

**Default admin login:**
```
Email:    admin@example.com
Password: admin123
```

Seriously, change the password. It's right at the top of the file:

```php
define('ADMIN_PASS', 'admin123'); // <-- PLEASE change this
```

## Configuration

Everything you'd want to change is at the top of `index.php`:

```php
define('DB_TYPE',   'sqlite');        // 'sqlite' or 'mysql'
define('SITE_NAME', 'Hazel+');
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_PASS',  'your-password-here');
```

For MySQL, also set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.

### Pretty URLs (optional)

If you want `/home` instead of `?page=home`, add this to your `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
```

## File structure

```
index.php       ← the whole app
uploads/        ← user-uploaded images (created automatically)
hazelplus.db    ← SQLite database (created automatically)
```

That's it. No composer, no npm, no build step.

## Contributing

Pull requests are welcome. A few things that would genuinely help:

- Better mobile layout
- Image compression on upload
- Search that actually works
- Email notifications
- Two-factor auth

If you find a bug, open an issue. If you fix a bug, open a PR.

## License

MIT — do whatever you want with it.

---

*Built by a teenager who wouldn't let Google+ die.*
