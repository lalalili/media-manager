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
    expect((new NullMediaTenantResolver())->canAccessManager(null))->toBeFalse();
});
