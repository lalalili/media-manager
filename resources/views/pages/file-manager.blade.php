<x-filament-panels::page>
    <div class="grid gap-4 lg:grid-cols-[18rem_1fr]">
        <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Folders</h2>
            </div>

            <form wire:submit="createFolder" class="mt-4 flex gap-2">
                <input
                    wire:model="folderName"
                    type="text"
                    class="min-w-0 flex-1 rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-950"
                    placeholder="New folder"
                >
                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-medium text-white">
                    Add
                </button>
            </form>

            <div class="mt-4 space-y-1">
                @forelse ($this->getFolders() as $folder)
                    <button
                        type="button"
                        wire:click="selectFolder({{ $folder->getKey() }})"
                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm {{ $selectedFolderId === $folder->getKey() ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                    >
                        <span class="truncate">{{ data_get($folder, 'name') }}</span>
                        <span class="text-xs text-gray-400">#{{ $folder->getKey() }}</span>
                    </button>
                @empty
                    <p class="rounded-md bg-gray-50 p-3 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        No folders found.
                    </p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Files</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->selectedFolder()?->getAttribute('name') ?? 'Select a folder' }}
                    </p>
                </div>
            </div>

            <form wire:submit="uploadFile" class="mt-4 flex flex-wrap items-center gap-3">
                <input wire:model="upload" type="file" class="text-sm">
                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-medium text-white disabled:opacity-50" @disabled(! $selectedFolderId)>
                    Upload
                </button>
            </form>

            <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->getSelectedFolderMedia() as $media)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-950 dark:text-white">{{ $media->file_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $media->human_readable_size }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ $media->getUrl() }}" target="_blank" class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                Open
                            </a>
                            <button type="button" wire:click="deleteMedia({{ $media->getKey() }})" class="text-sm font-medium text-danger-600 dark:text-danger-400">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="rounded-md bg-gray-50 p-3 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        No files in this folder.
                    </p>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
