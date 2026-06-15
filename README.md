# Media Manager

Reusable Filament and Livewire media manager package for Laravel applications.

## Features

- Configurable folder, video, and media model classes.
- Configurable Filament page registration with a starter File Manager page.
- Media tenant resolver contract for host-specific admin access rules.
- Command to ensure root folders exist.

## Installation

Require the package through Composer:

```bash
composer require lalalili/media-manager
```

When installing directly from GitHub before a Packagist release, add the VCS repository to the host application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/lalalili/media-manager.git"
        }
    ]
}
```

Publish and customize the configuration:

```bash
php artisan vendor:publish --tag=media-manager-config
```

Configure host model classes, Filament pages, folder type values, and tenant resolver:

```php
return [
    'models' => [
        'folder' => App\Models\Folder::class,
        'video' => App\Models\Video::class,
        'media' => App\Models\Media::class,
    ],

    'pages' => [
        App\Filament\Pages\FileManager::class,
    ],

    'tenant_resolver' => App\Services\Media\MediaTenantResolver::class,
];
```

Register the plugin in a Filament panel provider:

```php
use Lalalili\MediaManager\MediaManagerPlugin;

$panel->plugins([
    MediaManagerPlugin::make(),
]);
```

Create required root folders:

```bash
php artisan media-manager:ensure-root-folders --company-id=1 --user-id=1
```

The command intentionally requires tenant context. Do not rely on default company or user ids in production.

## Upload Center Boundary

This package remains a general media and folder manager. It does not own large video upload sessions, multipart upload orchestration, course unit binding, provider import jobs, or provider processing status refreshes.

Host applications may reuse its folder model configuration, folder type values, and tenant resolver when building an Upload Center, but the Upload Center flow should live in the host app or a course admin integration package such as `course-filament`.

Use tagged versions in host applications:

```bash
composer require lalalili/media-manager:^0.1
```

## Tests

From the package directory:

```bash
./vendor/bin/pest
```
