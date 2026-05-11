<?php

use Lalalili\MediaManager\MediaManagerPlugin;
use Lalalili\MediaManager\Pages\FileManager;
use Lalalili\MediaManager\Support\NullMediaTenantResolver;

it('provides the default file manager page through config', function (): void {
    expect(config('media-manager.pages'))->toContain(FileManager::class);
});

it('uses a stable plugin id', function (): void {
    expect(MediaManagerPlugin::make()->getId())->toBe('media-manager');
});

it('denies manager access by default', function (): void {
    expect((new NullMediaTenantResolver)->canAccessManager(null))->toBeFalse();
});

it('keeps reusable folder capabilities configurable for host upload flows', function (): void {
    expect(config('media-manager.folder_types'))->toHaveKeys([
        'root',
        'public_root',
        'private_root',
        'public',
        'private',
        'subfolder',
    ])->and(config('media-manager.upload_center'))->toBeNull();
});
