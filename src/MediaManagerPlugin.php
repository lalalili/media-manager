<?php

namespace Lalalili\MediaManager;

use Filament\Contracts\Plugin;
use Filament\Panel;

class MediaManagerPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'media-manager';
    }

    public function register(Panel $panel): void
    {
        $configuredPages = config('media-manager.pages', []);
        $pages = collect(is_array($configuredPages) ? $configuredPages : [])
            ->filter(fn (string $page): bool => class_exists($page))
            ->unique()
            ->values()
            ->all();

        if ($pages !== []) {
            $panel->pages($pages);
        }
    }

    public function boot(Panel $panel): void {}
}
