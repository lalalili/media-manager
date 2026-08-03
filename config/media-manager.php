<?php

use Lalalili\MediaManager\Pages\FileManager;
use Lalalili\MediaManager\Support\NullMediaTenantResolver;

return [
    'models' => [
        'folder' => null,
        'video' => null,
        'media' => null,
    ],

    'pages' => [
        FileManager::class,
    ],

    'folder_types' => [
        'root' => 1,
        'public_root' => 2,
        'private_root' => 3,
        'public' => 4,
        'private' => 5,
        'subfolder' => 6,
    ],

    'collections' => [
        'files' => 'files',
    ],

    'tenant_resolver' => NullMediaTenantResolver::class,
];
