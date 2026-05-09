<?php

namespace Lalalili\MediaManager\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Lalalili\MediaManager\Contracts\MediaTenantResolver;

class NullMediaTenantResolver implements MediaTenantResolver
{
    public function currentCompanyId(): ?int
    {
        return null;
    }

    public function isSuperAdmin(?Authenticatable $user): bool
    {
        return false;
    }

    public function canAccessManager(?Authenticatable $user): bool
    {
        return true;
    }
}
