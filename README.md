# Laravel Logs Viewer (Bootstrap 5)

Simple, zero-dependency package to view Laravel log files in your browser using Bootstrap 5.

## Installation

1. Require the package in your Laravel app:

```bash
composer require tong/laravel-logs-viewer
```

2. Visit the route:

```
/logs-viewer
```

Optionally publish config and views:

```bash
php artisan vendor:publish --tag=logs-viewer-config
php artisan vendor:publish --tag=logs-viewer-views
```

## Configuration

```php
// config/logs-viewer.php
return [
    'route_prefix' => 'logs-viewer',
    'middleware' => ['web'],
];
```

## Features

- Browse available `.log` files under `storage/logs`
- View entries parsed and grouped by header line
- Download current log file
- Clear current log file
- Bootstrap 5 UI (CDN)

## Security

Protect access using middleware in the config (e.g., `auth`, `can:admin`).

## License

MIT


