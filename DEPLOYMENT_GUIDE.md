# RM Manliquid Business System - Production Deployment Guide

## 🚀 Quick Deployment Steps

### 1. Pre-Deployment Checklist
- [ ] Run `npm run build` to compile assets ✓
- [ ] Upload all files to your server
- [ ] Set proper file permissions
- [ ] Configure database

### 2. Server Requirements
- PHP 8.1+
- MySQL 5.7+ or SQLite
- Node.js (for asset building)
- Web server (Apache/Nginx)

### 3. Environment Configuration

Create `.env` file on server using `production_env_template.txt`:

```bash
# On your server, copy the template and update values:
cp production_env_template.txt .env
# Edit .env with your actual values
```

**Critical Settings:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- Update database credentials
- Generate new APP_KEY

### 4. Database Setup

**Option A: MySQL (Recommended)**
```sql
CREATE DATABASE your_database_name;
CREATE USER 'your_database_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_database_user'@'localhost';
FLUSH PRIVILEGES;
```

**Option B: SQLite (Simple)**
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
```

### 5. Run Deployment Script
```bash
php deployment_fix.php
```

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Run Database Migrations
```bash
php artisan migrate --force
```

### 8. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Web Server Configuration

### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 🔒 Security Settings

### File Permissions
```bash
chmod -R 755 storage bootstrap/cache public
chmod -R 644 storage/logs/*.log
```

### Protect Sensitive Files
Ensure `.env`, `.git`, and other sensitive files are not accessible via web.

## 🐛 Common Issues & Fixes

### Issue: "404 Not Found" on routes
**Fix:** Ensure `mod_rewrite` is enabled (Apache) or configure Nginx properly.

### Issue: "500 Internal Server Error"
**Fix:** Check storage permissions and run `php artisan storage:link`.

### Issue: Assets not loading
**Fix:** Run `npm run build` and ensure `public/build` directory exists.

### Issue: Database connection failed
**Fix:** Verify `.env` database credentials and run migrations.

## 📱 Testing After Deployment

1. **Homepage:** Check if welcome page loads
2. **Login:** Test user authentication
3. **Dashboard:** Verify dashboard functionality
4. **Favicon:** Confirm RM logo appears in browser tab
5. **SSL:** Ensure HTTPS works properly

## 🔄 Maintenance Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize again
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update dependencies
composer update --no-dev
npm update
npm run build
```

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify server requirements
3. Ensure all steps above were completed
4. Test with `php artisan serve` locally first

---

**🎉 Your RM Manliquid Business System is now ready for production!**
