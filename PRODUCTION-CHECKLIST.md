# Production Deployment Checklist

## Pre-Deployment Requirements

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` with `php artisan key:generate`
- [ ] Configure database connection (MySQL/PostgreSQL recommended for production)
- [ ] Set up mail configuration (SMTP)
- [ ] Configure cache driver (Redis recommended)
- [ ] Configure session driver (Redis/database recommended)
- [ ] Configure queue driver (Redis/database recommended)

### 2. Security Settings
- [ ] Set strong database passwords
- [ ] Configure HTTPS/SSL certificate
- [ ] Set secure session cookies (`SESSION_SECURE_COOKIE=true`)
- [ ] Configure CORS settings if needed
- [ ] Review file upload security settings

### 3. Performance Optimization
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build` for production assets
- [ ] Enable OPcache in PHP
- [ ] Configure Redis for caching (if available)

## Deployment Steps

### 1. Upload Files
- [ ] Upload all project files to hosting server
- [ ] Ensure `public` folder is set as document root
- [ ] Upload `.env` file with production settings

### 2. Set File Permissions
```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 644 .env
```

### 3. Run Laravel Optimization Commands
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

### 4. Database Setup
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan db:seed` (if needed)
- [ ] Create storage link: `php artisan storage:link`

### 5. Web Server Configuration

#### Apache (.htaccess)
Ensure your hosting supports `.htaccess` files and mod_rewrite is enabled.

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## PWA-Specific Checklist

### 1. Service Worker
- [ ] Verify `/sw.js` route is accessible
- [ ] Test service worker registration in browser
- [ ] Verify offline functionality works
- [ ] Check cache versioning is correct

### 2. Manifest File
- [ ] Verify `manifest.json` is accessible
- [ ] Test PWA installation on mobile devices
- [ ] Verify icons are loading correctly

### 3. HTTPS Requirements
- [ ] Ensure site is served over HTTPS (required for PWA)
- [ ] Test service worker only works on HTTPS

## Post-Deployment Testing

### 1. Basic Functionality
- [ ] Test user registration/login
- [ ] Test admin panel access
- [ ] Test file uploads
- [ ] Test email sending

### 2. PWA Testing
- [ ] Test offline functionality
- [ ] Test "Add to Home Screen" prompt
- [ ] Test background sync (if implemented)
- [ ] Test push notifications (if implemented)

### 3. Performance Testing
- [ ] Run Google PageSpeed Insights
- [ ] Test loading times
- [ ] Verify asset compression
- [ ] Check database query performance

## Monitoring & Maintenance

### 1. Error Monitoring
- [ ] Set up error logging
- [ ] Configure log rotation
- [ ] Set up monitoring alerts

### 2. Backup Strategy
- [ ] Set up automated database backups
- [ ] Set up file system backups
- [ ] Test backup restoration process

### 3. Updates
- [ ] Plan for Laravel updates
- [ ] Plan for dependency updates
- [ ] Set up staging environment for testing

## Common Issues & Solutions

### 1. Service Worker Not Updating
- Clear browser cache
- Check cache versioning in service worker
- Verify proper cache headers

### 2. Assets Not Loading
- Check file permissions
- Verify asset paths in production
- Run `php artisan storage:link`

### 3. Database Connection Issues
- Verify database credentials
- Check database server accessibility
- Ensure database exists

### 4. Permission Errors
- Set proper file permissions (755 for directories, 644 for files)
- Ensure web server can write to storage and cache directories

## Support

For issues during deployment:
1. Check Laravel logs in `storage/logs/`
2. Check web server error logs
3. Verify all environment variables are set correctly
4. Test in staging environment first

---

**Note**: This checklist assumes a standard shared hosting or VPS environment. Adjust accordingly for your specific hosting setup.