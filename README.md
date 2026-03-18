# Document Verification System with QR Code Authentication

Full-stack Laravel application for uploading, managing, and publicly verifying official documents using unique QR verification codes.

This implementation uses plain HTML templates from the public folder (no Blade templates for the user interfaces).

## Core Features

- Admin authentication with API token
- Role-based access control for admin endpoints
- Document upload and management (PDF/JPG/PNG)
- Auto-generated unique verification code per document
- Public verification API and verification page
- Verification attempt audit logs (success, failed, revoked)
- File checksum tracking (SHA-256) for integrity

## Tech Stack

- Laravel 12
- MySQL or SQLite
- Plain HTML + JavaScript frontend pages
- Shared CSS design system in public/assets/ui.css

## Frontend Structure

- resources/views/html/index.html
- resources/views/html/admin/index.html
- resources/views/html/admin/login.html
- resources/views/html/admin/dashboard.html
- resources/views/html/verify/index.html
- resources/views/html/verify/check.html
- public/assets/ui.css

## Project Pages

- Landing page: /
- Admin home: /admin
- Admin login page: /admin/login
- Admin dashboard: /admin/dashboard
- Verification home: /verify
- Public verify page: /verify/check
- Verify by QR code path: /verify/{code}

## Default Admin Account

- Email: admin@docverify.local
- Password: Admin@12345

Change this immediately in production.

## Setup (XAMPP / Windows)

1. Copy and configure environment:
    - Copy .env.example to .env if needed
    - Set database values in .env

2. For MySQL (XAMPP), example values:
    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE=document_verification
    - DB_USERNAME=root
    - DB_PASSWORD=

3. Create application key:
    - php artisan key:generate

4. Run migrations and seed admin user:
    - php artisan migrate:fresh --seed

5. Start app:
    - Preferred (Laravel dev server): php artisan serve --host=127.0.0.1 --port=8000
    - If port 8000 is unavailable/hangs in your environment: php -S 127.0.0.1:8080 -t public

6. Open in browser:
    - Main: http://127.0.0.1:8000/ or http://127.0.0.1:8080/
    - Admin login: http://127.0.0.1:8000/admin/login or http://127.0.0.1:8080/admin/login

## Main API Endpoints

- POST /api/login
- POST /api/admin/login
- POST /api/logout
- GET /api/me
- GET /api/verify/{code}
- GET /api/admin/documents
- POST /api/admin/documents
- POST /api/admin/documents/{document}
- DELETE /api/admin/documents/{document}
- POST /api/admin/documents/{document}/revoke
- GET /api/admin/documents/{document}/download
- GET /api/admin/logs

Admin endpoints require Authorization: Bearer {token}.

## Notes

- Uploaded files are stored in the local storage disk.
- QR generation in the admin page is done client-side using qrcodejs.
- Verification logs capture attempted code, status, IP, and user agent.
- /login is redirected to /admin/login for compatibility.
