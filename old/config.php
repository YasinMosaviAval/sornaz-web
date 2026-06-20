<?php
if (!defined('APP_ENTRY')) { echo "Forbidden Request"; exit; }

// ── Load .env ──────────────────────────────────────────────────────────────
$envFile = getcwd() . '/.env';
if (!file_exists($envFile)) {
  die('.env file not found. Copy .env.example to .env and fill in your values.');
}

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
  if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
  [$key, $value] = explode('=', $line, 2);
  $_ENV[trim($key)] = trim($value);
}

function env(string $key, mixed $default = null): mixed {
  return $_ENV[$key] ?? $default;
}

// ── Config ─────────────────────────────────────────────────────────────────
global $config;

$config['db'] = [
  'host' => env('DB_HOST', 'localhost'),
  'user' => env('DB_USER', 'root'),
  'pass' => env('DB_PASS', ''),
  'name' => env('DB_NAME', ''),
];

$config['app'] = [
  'env'   => env('APP_ENV', 'local'),
  'salt'  => env('APP_SALT', ''),
  'base'  => env('APP_BASE', ''),
  'lang'  => env('APP_LANG', 'fa'),
  'theme' => env('APP_THEME', 'light'),
];

$config['page'] = [
  'title'    => env('PAGE_TITLE', 'My App'),
  'noindex'  => env('PAGE_NOINDEX', 'false') === 'true',
  'nofollow' => env('PAGE_NOFOLLOW', 'false') === 'true',
];

$config['zarinpal'] = [
  'merchantId' => env('ZARINPAL_MERCHANT_ID', ''),
];

$config['salt'] = env('APP_SALT');

// ── Routes ─────────────────────────────────────────────────────────────────
// Format: 'uri pattern' => 'controller/method'
// Use * for wildcard
$config['route'] = array_merge($config['route'] ?? [], [
  
  // '/'                               => '/page/home',
  // '/users'                             => '/user/list',
  // '/users/*'                           => '/user/show/*',
  '/login'                             => '/account/login',
  // '/login'                             => '/account/loginForm',
  '/logout'                            => '/account/logout',
  '/register'                          => '/account/register',
  // '/register'                          => '/account/registerForm',
  // '/profile'                           => '/account/profileView',
  // '/account/profile'                   => '/account/profileView',
  // '/ورود'                              => '/account/loginForm',
  '/account/verify-email'              => '/account/verifyEmail',
  '/account/verify-phone'              => '/account/verifyPhoneForm',
  '/account/resend-otp'                => '/account/resendOtp',
  '/account/forgot-password'           => '/account/forgotForm',
  '/account/reset-password'            => '/account/resetForm',
  '/account/google'                    => '/account/googleRedirect',
  '/account/google/callback'           => '/account/googleCallback',
  // ── Messages ─────────────────────────────────────────────
  '/message'                           => '/message/inbox',
  '/message/show/*'                    => '/message/show/$1',
  '/message/start/*'                   => '/message/start/$1',
  '/message/send/*'                    => '/message/send/$1',
  '/message/edit/*'                    => '/message/edit/$1',
  '/message/delete/*'                  => '/message/delete/$1',
  '/message/upload/*'                  => '/message/upload/$1',
  '/message/leave/*'                   => '/message/leave/$1',
  '/message/mute/*'                    => '/message/mute/$1',
  '/message/delete-conv/*'             => '/message/deleteConv/$1',
  '/message/group/create'              => '/message/createGroup',
  // ── Admin Posts & Comments ───────────────────────────────
  '/admin/posts'                       => '/admin/posts',
  '/admin/posts/approve/*'             => '/admin/approvePost/$1',
  '/admin/posts/reject/*'              => '/admin/rejectPost/$1',
  '/admin/posts/force-delete/*'        => '/admin/forceDeletePost/$1',
  '/admin/comments'                    => '/admin/comments',
  '/admin/comments/approve/*'          => '/admin/approveComment/$1',
  '/admin/comments/reject/*'           => '/admin/rejectComment/$1',
  '/admin/comments/approve-many'       => '/admin/approveManyComments',
  // ── Posts ────────────────────────────────────────────────
  '/post'                              => '/post/list',
  '/post/show/*'                       => '/post/show/$1',
  '/post/create'                       => '/post/createForm',
  '/post/edit/*'                       => '/post/editForm/$1',
  '/post/delete/*'                     => '/post/delete/$1',
  '/post/my'                           => '/post/myPosts',
  // ── Comments ─────────────────────────────────────────────
  '/comment/add'                       => '/comment/add',
  '/comment/edit/*'                    => '/comment/edit/$1',
  '/comment/delete/*'                  => '/comment/delete/$1',
  // ── Admin Users ───────────────────────────────────────────
  // '/admin/users'                       => '/admin/users',
  // '/admin/users/show/*'                => '/admin/users/show/$1',
  // '/admin/users/update/*'              => '/admin/users/update/$1',
  // '/admin/users/status/*'              => '/admin/users/updateStatus/$1',
  // '/admin/users/reset-password/*'      => '/admin/users/resetPassword/$1',
  // '/admin/users/approve/*'             => '/admin/users/approve/$1',
  // '/admin/users/delete/*'              => '/admin/users/delete/$1',
  // '/admin/users/sessions/revoke/*'     => '/admin/users/revokeSession/$1',
  // ── Admin User Access ─────────────────────────────────────
  // '/admin/users/role/assign/*'        => '/admin/users/assignRole/$1',
  // '/admin/users/role/revoke/*'        => '/admin/users/revokeRole/$1',
  // '/admin/users/permission/grant/*'   => '/admin/users/grantPermission/$1',
  // '/admin/users/permission/revoke/*'  => '/admin/users/revokePermission/$1',
]);


// ── Mail ─────────────────────────────────────────────────────────────────
$config['mail'] = [
  'host'       => env('MAIL_HOST',      'localhost'),
  'port'       => env('MAIL_PORT',      '587'),
  'encryption' => env('MAIL_ENCRYPTION','tls'),
  'user'       => env('MAIL_USER',      ''),
  'pass'       => env('MAIL_PASS',      ''),
  'from'       => env('MAIL_FROM',      'no-reply@sornaz.com'),
  'from_name'  => env('MAIL_FROM_NAME', 'Sornaz Academy'),
];

// ── SMS ─────────────────────────────────────────────────────────────────
$config['sms'] = [
  'provider'            => env('SMS_PROVIDER',            'kavenegar'),
  'api_key'             => env('SMS_API_KEY',             ''),
  'sender'              => env('SMS_SENDER',              ''),
  'kavenegar_template'  => env('SMS_KAVENEGAR_TEMPLATE',  ''),
  // 'username'            => env('SMS_USERNAME',            ''), // ملی‌پیامک
  // 'password'            => env('SMS_PASSWORD',            ''), // ملی‌پیامک
  // 'from'                => env('SMS_FROM',                ''), // ملی‌پیامک
];


// ── Google OAuth ─────────────────────────────────────────────────────────────────
$config['google'] = [
  'client_id'     => env('GOOGLE_CLIENT_ID',     ''),
  'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
  'redirect_uri'  => env('GOOGLE_REDIRECT_URI',  ''),
];


