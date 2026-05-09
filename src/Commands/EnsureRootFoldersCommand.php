<?php

namespace Lalalili\MediaManager\Commands;

use Illuminate\Console\Command;

class EnsureRootFoldersCommand extends Command
{
    protected $signature = 'media-manager:ensure-root-folders {--company-id=1} {--user-id=1}';

    protected $description = 'Ensure media manager root, public root, and private root folders exist.';

    public function handle(): int
    {
        $folderModel = config('media-manager.models.folder');

        if (! is_string($folderModel) || ! class_exists($folderModel)) {
            $this->error('Configured folder model does not exist.');

            return self::FAILURE;
        }

        $companyId = (int) $this->option('company-id');
        $userId = (int) $this->option('user-id');
        $root = $this->firstOrCreateFolder($folderModel, 'ROOT', 'root', $this->folderType('root'), null, $companyId, $userId);

        $this->firstOrCreateFolder($folderModel, 'PUBLIC_ROOT', 'public-root', $this->folderType('public_root'), $root->getKey(), $companyId, $userId);
        $this->firstOrCreateFolder($folderModel, 'PRIVATE_ROOT', 'private-root', $this->folderType('private_root'), $root->getKey(), $companyId, $userId);

        $this->info('Media manager root folders are ready.');

        return self::SUCCESS;
    }

    protected function firstOrCreateFolder(string $folderModel, string $name, string $slug, mixed $type, ?int $parentId, int $companyId, int $userId): object
    {
        return $folderModel::withoutGlobalScopes()->firstOrCreate(
            ['type' => $type],
            [
                'name'       => $name,
                'slug'       => $slug,
                'parent_id'  => $parentId,
                'company_id' => $companyId,
                'user_id'    => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        );
    }

    protected function folderType(string $key): mixed
    {
        return config("media-manager.folder_types.{$key}");
    }
}
