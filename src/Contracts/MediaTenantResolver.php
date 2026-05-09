<?php

namespace Lalalili\MediaManager\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface MediaTenantResolver
{
    public function currentCompanyId(): ?int;

    public function isSuperAdmin(?Authenticatable $user): bool;

    public function canAccessManager(?Authenticatable $user): bool;
}
