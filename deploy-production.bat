@echo off
echo Starting Production Deployment...
echo.

echo Step 1: Installing/Updating Composer dependencies...
composer install --optimize-autoloader --no-dev
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo.
echo Step 2: Building assets with Vite...
npm run build
if %errorlevel% neq 0 (
    echo ERROR: Asset build failed!
    pause
    exit /b 1
)

echo.
echo Step 3: Optimizing Laravel configuration...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo.
echo Step 4: Running database migrations (if needed)...
php artisan migrate --force

echo.
echo Step 5: Clearing and optimizing caches...
php artisan optimize
php artisan storage:link

echo.
echo Step 6: Setting proper permissions...
echo Please ensure the following directories are writable:
echo - storage/
echo - bootstrap/cache/
echo - public/storage/

echo.
echo Production deployment completed successfully!
echo.
echo IMPORTANT REMINDERS:
echo 1. Update your .env file with production settings
echo 2. Set APP_ENV=production and APP_DEBUG=false
echo 3. Configure your database connection
echo 4. Set up proper SSL certificate
echo 5. Configure your web server (Apache/Nginx)
echo.
pause