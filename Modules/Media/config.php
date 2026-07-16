<?php

return [
    'disk' => 'public',
    'directory' => [
        'logo' => 'uploads/logo',
        'cover' => 'uploads/cover',
        'gallery' => 'uploads/gallery',
        'video' => 'uploads/video',
        'document' => 'uploads/document',
    ],
    'image_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp'
    ],
    'max_size' => 20 * 1024 * 1024,
];