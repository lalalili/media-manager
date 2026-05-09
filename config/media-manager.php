<?php

return [
    'models' => [
        'folder' => null,
        'video'  => null,
        'media'  => null,
    ],

    'pages' => [],

    'folder_types' => [
        'root'         => 1,
        'public_root'  => 2,
        'private_root' => 3,
        'public'       => 4,
        'private'      => 5,
        'subfolder'    => 6,
    ],

    'tenant_resolver' => Lalalili\MediaManager\Support\NullMediaTenantResolver::class,
];
