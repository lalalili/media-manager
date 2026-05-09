<?php

namespace Lalalili\MediaManager\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Lalalili\MediaManager\Contracts\MediaTenantResolver;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class FileManager extends Page
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Folder;

    protected static string | \UnitEnum | null $navigationGroup = 'Media';

    protected static ?string $title = 'File Manager';

    protected static ?int $navigationSort = 30;

    protected string $view = 'media-manager::pages.file-manager';

    public ?int $selectedFolderId = null;

    public ?string $folderName = null;

    public mixed $upload = null;

    public function getTitle(): string | Htmlable
    {
        return 'File Manager';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return app(MediaTenantResolver::class)->canAccessManager(auth()->user());
    }

    /**
     * @return EloquentCollection<int, Model>
     */
    public function getFolders(): EloquentCollection
    {
        $folderModel = $this->folderModel();

        if (! $folderModel) {
            return new EloquentCollection();
        }

        return $folderModel::query()
            ->withoutGlobalScopes()
            ->orderBy('parent_id')
            ->orderBy('name')
            ->limit(200)
            ->get();
    }

    public function createFolder(MediaTenantResolver $tenantResolver): void
    {
        $this->validate([
            'folderName' => ['required', 'string', 'max:120'],
        ]);

        $folderModel = $this->folderModel();

        if (! $folderModel) {
            $this->configurationError();

            return;
        }

        /** @var Model $folder */
        $folder = new $folderModel();
        $table = $folder->getTable();
        $attributes = [
            'name'      => $this->folderName,
            'slug'      => str($this->folderName)->slug()->toString(),
            'parent_id' => $this->selectedFolderId,
        ];

        if (Schema::hasColumn($table, 'type')) {
            $attributes['type'] = config('media-manager.folder_types.subfolder');
        }

        if (Schema::hasColumn($table, 'company_id')) {
            $attributes['company_id'] = $tenantResolver->currentCompanyId();
        }

        if (Schema::hasColumn($table, 'user_id')) {
            $attributes['user_id'] = auth()->id();
        }

        if (Schema::hasColumn($table, 'created_by')) {
            $attributes['created_by'] = auth()->id();
        }

        if (Schema::hasColumn($table, 'updated_by')) {
            $attributes['updated_by'] = auth()->id();
        }

        $folder->forceFill($attributes)->save();
        $this->folderName = null;

        Notification::make()
            ->success()
            ->title('Folder created')
            ->send();
    }

    public function uploadFile(): void
    {
        $this->validate([
            'selectedFolderId' => ['required', 'integer'],
            'upload'           => ['required', 'file', 'max:102400'],
        ]);

        $folder = $this->selectedFolder();

        if (! $folder instanceof HasMedia || ! method_exists($folder, 'addMedia')) {
            $this->configurationError('The configured folder model must implement Spatie\\MediaLibrary\\HasMedia.');

            return;
        }

        $folder
            ->addMedia($this->upload->getRealPath())
            ->usingFileName($this->upload->getClientOriginalName())
            ->toMediaCollection(config('media-manager.collections.files', 'files'));

        $this->upload = null;

        Notification::make()
            ->success()
            ->title('File uploaded')
            ->send();
    }

    public function deleteMedia(int $mediaId): void
    {
        $mediaModel = config('media-manager.models.media') ?: \Spatie\MediaLibrary\MediaCollections\Models\Media::class;

        if (! is_string($mediaModel) || ! class_exists($mediaModel)) {
            $this->configurationError('The configured media model does not exist.');

            return;
        }

        $mediaModel::query()->findOrFail($mediaId)->delete();

        Notification::make()
            ->success()
            ->title('File deleted')
            ->send();
    }

    public function selectFolder(int $folderId): void
    {
        $this->selectedFolderId = $folderId;
    }

    public function selectedFolder(): ?Model
    {
        $folderModel = $this->folderModel();

        if (! $folderModel || ! $this->selectedFolderId) {
            return null;
        }

        return $folderModel::withoutGlobalScopes()->find($this->selectedFolderId);
    }

    public function getSelectedFolderMedia(): MediaCollection
    {
        $folder = $this->selectedFolder();

        if (! $folder instanceof HasMedia || ! method_exists($folder, 'getMedia')) {
            return new MediaCollection();
        }

        return $folder->getMedia(config('media-manager.collections.files', 'files'));
    }

    protected function folderModel(): ?string
    {
        $folderModel = config('media-manager.models.folder');

        return is_string($folderModel) && class_exists($folderModel) ? $folderModel : null;
    }

    protected function configurationError(string $message = 'The media-manager package is not configured correctly.'): void
    {
        Notification::make()
            ->danger()
            ->title($message)
            ->send();
    }
}
