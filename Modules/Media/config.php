<?php

return [
    'disk' => 'public',
    'directory' => [
        'logo' => 'uploads/logo',
        'cover' => 'uploads/cover',
        'gallery' => 'uploads/gallery',
        'video' => 'uploads/video',
        'intro_video'=>'uploads/intro_video',
        'academy_video' => 'uploads/academy_video',
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