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
        $pages = collect(config('media-manager.pages', []))
            ->filter(fn (string $page): bool => class_exists($page))
            ->unique()
            ->values()
            ->all();

        if ($pages !== []) {
            $panel->pages($pages);
        }
    }

    public function boot(Panel $panel): void
    {
    }
}
