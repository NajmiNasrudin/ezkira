# SME Finance Monitor — Setup Guide

---

## 1. XAMPP Local Development (http://localhost/fikira)

### Step 1 — Place files
Copy the entire project folder to:
```
C:\xampp\htdocs\fikira\
```

### Step 2 — Start services
Open XAMPP Control Panel → Start **Apache** and **MySQL**.

### Step 3 — Create database
Open `http://localhost/phpmyadmin` in your browser.
1. Click **New** → Database name: `fikira_db` → Encoding: `utf8mb4_unicode_ci` → **Create**
2. Select `fikira_db` → click **Import** tab
3. Import `database/schema.sql` → click **Go**
4. Import `database/seed.sql` → click **Go**

### Step 4 — Configure app
Edit `config/config.php`:
```php
define('APP_URL',  'http://localhost/fikira');
define('APP_ENV',  'development');
define('APP_DEBUG', true);
define('DB_HOST',  'localhost');
define('DB_NAME',  'fikira_db');
define('DB_USER',  'root');
define('DB_PASS',  '');           // default XAMPP password is empty
define('BASE_URI', '/fikira');    // matches your subfolder path
```

### Step 5 — Access the app
Open: `http://localhost/fikira`

Login with the seeded admin account:
- **Email:** `admin@fikira.com`
- **Password:** `Admin@1234`

---

## 2. Shared cPanel Hosting Deployment

### Step 1 — Upload files
Use cPanel File Manager or an FTP client to upload all project files to:
```
/public_html/        (if app is at domain root)
/public_html/fikira/ (if app is at yourdomain.com/fikira)
```

### Step 2 — Create MySQL database
In cPanel → **MySQL Databases**:
1. Create database: e.g. `youracc_fikira`
2. Create user: e.g. `youracc_fikira_user` with a strong password
3. Add user to database → grant **All Privileges**

### Step 3 — Import schema
cPanel → **phpMyAdmin** → select your database → **Import** → upload `database/schema.sql` then `database/seed.sql`.

### Step 4 — Configure production config
Create/edit `config/config.php` on the server (do NOT use the dev version):
```php
define('APP_URL',   'https://yourdomain.com');
define('APP_ENV',   'production');
define('APP_DEBUG',  false);
define('DB_HOST',   'localhost');
define('DB_NAME',   'youracc_fikira');
define('DB_USER',   'youracc_fikira_user');
define('DB_PASS',   'yourStrongPassword');
define('BASE_URI',  '');           // '' for domain root, '/fikira' for subdirectory
```

### Step 5 — Set file permissions
In cPanel File Manager or via SSH:
```
uploads/profiles/  → chmod 755
storage/logs/      → chmod 755
storage/sessions/  → chmod 755
config/config.php  → chmod 444  (prevents FTP overwrite)
```

### Step 6 — Set PHP version
In cPanel → **PHP Selector** (or **MultiPHP Manager**):
- Set PHP version to **8.1** or higher for this domain.

### Step 7 — Enable SSL
In cPanel → **SSL/TLS** → **Let's Encrypt** → Issue certificate for your domain.

Then uncomment the HTTPS redirect in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 3. GitHub Actions Auto-Deploy (FTP)

### Step 1 — Push code to GitHub
Initialize the repository and push to `main` branch:
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/fikira.git
git push -u origin main
```

### Step 2 — Add GitHub Secrets
In your GitHub repository → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**:

| Secret name    | Value                                          |
|----------------|------------------------------------------------|
| `FTP_HOST`     | Your FTP hostname (e.g. `ftp.yourdomain.com`)  |
| `FTP_USER`     | Your FTP username                              |
| `FTP_PASS`     | Your FTP password                              |
| `FTP_PROTOCOL` | `ftps`                                         |
| `FTP_DIR`      | `/public_html/` or `/public_html/fikira/`      |

### Step 3 — Trigger deploy
Every push to `main` automatically deploys via FTP.

Check **Actions** tab in GitHub to monitor deploy progress.

### Notes
- `config/config.php` is **excluded** from FTP deploy (protected by workflow)
- `uploads/profiles/` is excluded — user uploads stay on server
- First deployment: manually upload `config/config.php` with production values, then set `chmod 444`

---

## 4. Default Login Credentials

| Role  | Email                 | Password    |
|-------|-----------------------|-------------|
| Admin | admin@fikira.com      | Admin@1234  |

**Change the admin password immediately after first login!**

---

## 5. Folder Permissions Summary

| Path                  | Permission | Notes                          |
|-----------------------|------------|--------------------------------|
| `uploads/profiles/`   | 755        | PHP must write here            |
| `storage/logs/`       | 755        | PHP error logs                 |
| `storage/sessions/`   | 755        | PHP session storage            |
| `config/config.php`   | 444        | Read-only, prevents overwrite  |
| All `.php` files      | 644        | Readable by server             |
| All directories       | 755        | Standard web directory perms   |
