## 🚨 Laravel Exception Tracker

[![Packagist Version](https://img.shields.io/packagist/v/sergeahouansinou/laravel-exception-tracker.svg?style=flat-square)](https://packagist.org/packages/sergeahouansinou/laravel-exception-tracker)
[![Packagist Downloads](https://img.shields.io/packagist/dt/sergeahouansinou/laravel-exception-tracker.svg?style=flat-square)](https://packagist.org/packages/sergeahouansinou/laravel-exception-tracker)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

A self-hosted Laravel package for tracking and logging exceptions with automatic email notifications. Inspired by Sentry, but 100% under your control — no data sent to third parties.

## ✨ Features

- 📦 **Automatic exception capture** via Laravel's `reportable()` hook — never replaces the existing Handler
- 💾 **Database storage** in a dedicated `exception_logs` table
- 📬 **Professional HTML email notifications** with a Sentry-inspired template
- 🔒 **Sensitive data filtering** (passwords, tokens, secrets masked automatically)
- ⚙️ **Configurable exception filtering** — ignore ValidationException, AuthenticationException, etc.
- 🚀 **Async email sending** via Laravel queues (non-blocking)
- 🧹 **Artisan command** to purge old logs
- 📡 **REST API** to consult exception logs
- 🧰 Compatible with **Laravel 9 to 12**

## 📋 Requirements

- **PHP** >= 8.0
- **Laravel** 9, 10, 11, or 12
- A configured **database** connection (MySQL, PostgreSQL, SQLite, etc.)
- A configured **mail** driver (for email notifications)

## 🚀 Quick Start — Adding to a Laravel Project

Follow these steps to integrate Laravel Exception Tracker into a new or existing Laravel project.

### Step 1 — Create a Laravel project (skip if you already have one)

```bash
composer create-project laravel/laravel my-project
cd my-project
```

### Step 2 — Install the package via Composer

```bash
composer require sergeahouansinou/laravel-exception-tracker
```

> **Auto-discovery**: The service provider (`ExceptionTrackerServiceProvider`) is registered automatically via Laravel's package auto-discovery. No manual registration in `config/app.php` is needed.

### Step 3 — Publish configuration and views

```bash
php artisan vendor:publish --tag=exception-tracker-config
php artisan vendor:publish --tag=exception-tracker-views
```

### Step 4 — Run migrations

```bash
php artisan migrate
```

This creates the `exception_logs` table in your database.

### Step 5 — Configure your `.env`

Add the following variables to your `.env` file:

```env
# Exception Tracker
EXCEPTION_TRACKER_ENABLED=true
EXCEPTION_TRACKER_EMAIL_ENABLED=true
EXCEPTION_TRACKER_RECIPIENTS=admin@example.com,dev@example.com
EXCEPTION_TRACKER_QUEUE_ENABLED=true
EXCEPTION_TRACKER_QUEUE_CONNECTION=null
EXCEPTION_TRACKER_QUEUE_NAME=default
```

> **Note**: By default, tracking is **disabled** in the `local` and `testing` environments. To enable it locally, remove `local` from the `disabled_environments` array in `config/exception-tracker.php`.

### Step 6 — Configure mail (for email notifications)

Make sure your Laravel project has a working mail driver configured in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 7 — (Optional) Start a queue worker for async emails

If `EXCEPTION_TRACKER_QUEUE_ENABLED=true` (the default), emails are sent via a Laravel queue. Start a worker to process them:

```bash
php artisan queue:work
```

That's it! Laravel Exception Tracker is now active. Any unhandled exception in your application will be automatically captured, stored in the database, and reported by email.

---

## ⚙️ Installation

### 1. Install via Composer

```bash
composer require sergeahouansinou/laravel-exception-tracker
```

### 2. Publish configuration and views

```bash
php artisan vendor:publish --tag=exception-tracker-config
php artisan vendor:publish --tag=exception-tracker-views
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Configure your `.env`

```env
EXCEPTION_TRACKER_ENABLED=true
EXCEPTION_TRACKER_EMAIL_ENABLED=true
EXCEPTION_TRACKER_RECIPIENTS=admin@example.com,dev@example.com
EXCEPTION_TRACKER_QUEUE_ENABLED=true
EXCEPTION_TRACKER_QUEUE_CONNECTION=null
EXCEPTION_TRACKER_QUEUE_NAME=default
```

## 🛠️ Configuration

The published config file (`config/exception-tracker.php`) supports:

| Option | Description | Default |
|---|---|---|
| `enabled` | Enable/disable exception tracking | `true` |
| `email_enabled` | Enable/disable email notifications | `true` |
| `recipients` | List of email addresses to notify | `[]` |
| `queue.enabled` | Send emails via queue (async) | `true` |
| `queue.connection` | Queue connection name | `null` |
| `queue.queue` | Queue name | `default` |
| `ignored_exceptions` | Exception classes to skip | ValidationException, AuthenticationException, etc. |
| `disabled_environments` | Environments where tracking is disabled | `['local', 'testing']` |
| `sensitive_fields` | Fields to mask in request data | `['password', 'token', 'secret', ...]` |
| `stack_trace_limit` | Max stack trace frames | `20` |
| `max_days` | Days to retain logs | `30` |

## 📧 Email Template

The email template is a professional, responsive HTML design inspired by Sentry, with clear sections:

- **Error Summary** — Exception class, message, file and line
- **Stack Trace** — Formatted, limited, monospace display
- **Request Details** — URL, method, IP address
- **Request Headers & Body** — With sensitive data masked
- **Authenticated User** — ID and email
- **Environment** — App name, environment, PHP/Laravel versions, server, timestamp, request ID

The template is publishable and fully customizable:

```bash
php artisan vendor:publish --tag=exception-tracker-views
```

## 📝 Usage

### Automatic Capture

The package hooks into Laravel's exception handler via `reportable()`. All unhandled exceptions are automatically captured and reported — no code changes needed.

### Manual Tracking

```php
use ExceptionTracker\ExceptionTracker;

try {
    // risky operation
} catch (\Throwable $e) {
    ExceptionTracker::handle($e);
}
```

Or use the helper function:

```php
exception_tracker_log($e);
```

### Middleware (Optional)

For route-specific tracking:

```php
use ExceptionTracker\Http\Middleware\TrackExceptions;

Route::middleware(TrackExceptions::class)->group(function () {
    // your routes
});
```

## 📡 REST API

- `GET /api/exception-tracker` — List exception logs (paginated)
- `GET /api/exception-tracker/{id}` — Get a single exception log

## 🧹 Artisan Commands

```bash
# Purge old exception logs
php artisan exception-tracker:clear
```

## 🏗️ Architecture

| Class | Role |
|---|---|
| `ExceptionTrackerServiceProvider` | Registers config, views, migrations, routes, commands, and hooks into `reportable()` |
| `ExceptionTracker` | Orchestrator — filters, builds payload, stores, and notifies |
| `PayloadBuilder` | Builds structured error payloads with HTTP context, user, and environment |
| `ExceptionOccurred` | Mailable class for sending HTML email notifications |
| `ExceptionLog` | Eloquent model for the `exception_logs` table |
| `TrackExceptions` | Middleware for route-specific exception tracking |

## 🔒 Security & Performance

- **Fail-safe**: All internal errors are caught and logged — never crashes the host application
- **Non-blocking**: Email sending via Laravel queues by default
- **Data privacy**: Sensitive fields (passwords, tokens, secrets) are masked automatically
- **No host modification**: Uses `reportable()` — never replaces or wraps the Laravel exception handler

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.
