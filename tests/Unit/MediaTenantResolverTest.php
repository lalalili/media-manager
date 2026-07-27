<?php

/**
 * MediaTenantResolver 同時掌管存取控制(canAccessManager)與租戶隔離
 * (currentCompanyId),而套件只提供 Null 實作 —— 真正的實作由宿主透過
 * config('media-manager.tenant_resolver') 換入。
 *
 * 這個換入點先前完全沒有測試:如果 binding 壞掉退回 Null 實作,
 * FileManager 會對所有人隱藏(fail closed,還算安全);但若 binding
 * 沒有真的委派給宿主的實作,宿主的權限判斷就形同虛設。
 */

use Illuminate\Contracts\Auth\Authenticatable;
use Lalalili\MediaManager\Contracts\MediaTenantResolver;
use Lalalili\MediaManager\Pages\FileManager;
use Lalalili\MediaManager\Support\NullMediaTenantResolver;

/** 模擬宿主提供的實作 */
final class FakeTenantResolver implements MediaTenantResolver
{
    public function __construct(
        private readonly ?int $companyId = 42,
        private readonly bool $superAdmin = true,
        private readonly bool $canAccess = true,
    ) {}

    public function currentCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function isSuperAdmin(?Authenticatable $user): bool
    {
        return $this->superAdmin;
    }

    public function canAccessManager(?Authenticatable $user): bool
    {
        return $this->canAccess;
    }
}

it('defaults to the null resolver', function () {
    expect(config('media-manager.tenant_resolver'))
        ->toBe(NullMediaTenantResolver::class)
        ->and(app(MediaTenantResolver::class))
        ->toBeInstanceOf(NullMediaTenantResolver::class);
});

it('denies access by default so an unconfigured host fails closed', function () {
    expect(app(MediaTenantResolver::class)->canAccessManager(null))->toBeFalse()
        ->and(FileManager::shouldRegisterNavigation())->toBeFalse();
});

it('resolves the implementation named in config', function () {
    config()->set('media-manager.tenant_resolver', FakeTenantResolver::class);

    expect(app(MediaTenantResolver::class))->toBeInstanceOf(FakeTenantResolver::class);
});

it('delegates the navigation gate to the host resolver', function () {
    config()->set('media-manager.tenant_resolver', FakeTenantResolver::class);
    app()->bind(MediaTenantResolver::class, fn () => new FakeTenantResolver(canAccess: true));

    expect(FileManager::shouldRegisterNavigation())->toBeTrue();

    app()->bind(MediaTenantResolver::class, fn () => new FakeTenantResolver(canAccess: false));

    expect(FileManager::shouldRegisterNavigation())->toBeFalse();
});

it('exposes the tenant id the host reports', function () {
    app()->bind(MediaTenantResolver::class, fn () => new FakeTenantResolver(companyId: 7));

    expect(app(MediaTenantResolver::class)->currentCompanyId())->toBe(7);
});

it('keeps the null resolver tenant-less and unprivileged', function () {
    $resolver = new NullMediaTenantResolver;

    expect($resolver->currentCompanyId())->toBeNull()
        ->and($resolver->isSuperAdmin(null))->toBeFalse()
        ->and($resolver->canAccessManager(null))->toBeFalse();
});
