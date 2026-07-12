<?php

$appHost = parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';

return [
    'root_domain' => env('PLATFORM_ROOT_DOMAIN', $appHost),
    'academy_template_path' => public_path('academy'),
    'academy_template_asset_path' => '/academy/assets/',
    'teacher_template_path' => public_path('teacher'),
    'teacher_template_asset_path' => '/teacher/assets/',
];
