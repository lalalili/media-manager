<?php

namespace Lalalili\MediaManager;

use Lalalili\MediaManager\Commands\EnsureRootFoldersCommand;
use Lalalili\MediaManager\Contracts\MediaTenantResolver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MediaManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('media-manager')
            ->hasConfigFile('media-manager')
            ->hasViews();
    }

    public function registeringPackage(): void
    {
        $this->app->bind(MediaTenantResolver::class, fn ($app) => $app->make(config('media-manager.tenant_resolver')));
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                EnsureRootFoldersCommand::class,
            ]);
        }
    }
}
