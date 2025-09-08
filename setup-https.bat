@echo off
echo Setting up HTTPS for PWA testing...
echo.

REM Check if OpenSSL is available
openssl version >nul 2>&1
if %errorlevel% neq 0 (
    echo OpenSSL not found. Please install OpenSSL or use Git Bash which includes it.
    echo You can also use Windows Subsystem for Linux (WSL).
    pause
    exit /b 1
)

REM Create SSL directory
if not exist "ssl" mkdir ssl
cd ssl

echo Generating self-signed certificate...
echo.

REM Generate private key
openssl genrsa -out server.key 2048

REM Generate certificate signing request
openssl req -new -key server.key -out server.csr -subj "/C=US/ST=State/L=City/O=Organization/CN=192.168.109.182"

REM Generate self-signed certificate
openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt

echo.
echo Certificate generated successfully!
echo.
echo To start HTTPS server, run:
echo php -S 0.0.0.0:8443 -t ../public
echo.
echo Then access via: https://192.168.109.182:8443/pwa-test.html
echo.
echo Note: You'll need to accept the security warning for the self-signed certificate.
echo.
pause