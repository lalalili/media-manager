<?php

namespace Lalalili\MediaManager\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Lalalili\MediaManager\Contracts\MediaTenantResolver;

class EnsureRootFoldersCommand extends Command
{
    protected $signature = 'media-manager:ensure-root-folders {--company-id=} {--user-id=}';

    protected $description = 'Ensure media manager root, public root, and private root folders exist.';

    public function handle(MediaTenantResolver $tenantResolver): int
    {
        $folderModel = config('media-manager.models.folder');

        if (! is_string($folderModel) || ! class_exists($folderModel)) {
            $this->error('Configured folder model does not exist.');

            return self::FAILURE;
        }

        $companyId = $this->resolveCompanyId($tenantResolver);
        $userId = $this->resolveUserId();

        if (! $companyId || ! $userId) {
            $this->error('A company id and user id are required. Pass --company-id and --user-id, or run this command in an authenticated tenant context.');

            return self::FAILURE;
        }

        $root = $this->firstOrCreateFolder($folderModel, 'ROOT', 'root', $this->folderType('root'), null, $companyId, $userId);

        $this->firstOrCreateFolder($folderModel, 'PUBLIC_ROOT', 'public-root', $this->folderType('public_root'), $root->getKey(), $companyId, $userId);
        $this->firstOrCreateFolder($folderModel, 'PRIVATE_ROOT', 'private-root', $this->folderType('private_root'), $root->getKey(), $companyId, $userId);

        $this->info('Media manager root folders are ready.');

        return self::SUCCESS;
    }

    protected function firstOrCreateFolder(string $folderModel, string $name, string $slug, mixed $type, ?int $parentId, int $companyId, int $userId): Model
    {
        /** @var Model $model */
        $model = new $folderModel();
        $table = $model->getTable();
        $type = $this->normalizeFolderType($type);
        $attributes = ['type' => $type];
        $values = [
            'name'      => $name,
            'slug'      => $slug,
            'parent_id' => $parentId,
        ];

        if (Schema::hasColumn($table, 'company_id')) {
            $attributes['company_id'] = $companyId;
            $values['company_id'] = $companyId;
        }

        if (Schema::hasColumn($table, 'user_id')) {
            $values['user_id'] = null;
        }

        if (Schema::hasColumn($table, 'created_by')) {
            $values['created_by'] = $userId;
        }

        if (Schema::hasColumn($table, 'updated_by')) {
            $values['updated_by'] = $userId;
        }

        /** @var Model $folder */
        $folder = $folderModel::withoutGlobalScopes()->firstOrCreate($attributes, $values);

        return $folder;
    }

    protected function resolveCompanyId(MediaTenantResolver $tenantResolver): ?int
    {
        $companyId = $this->option('company-id') ?: $tenantResolver->currentCompanyId();

        return is_numeric($companyId) ? (int) $companyId : null;
    }

    protected function resolveUserId(): ?int
    {
        $userId = $this->option('user-id') ?: auth()->id();

        return is_numeric($userId) ? (int) $userId : null;
    }

    protected function normalizeFolderType(mixed $type): mixed
    {
        if ($type instanceof BackedEnum) {
            return $type->value;
        }

        return $type;
    }

    protected function folderType(string $key): mixed
    {
        return config("media-manager.folder_types.{$key}");
    }
}
