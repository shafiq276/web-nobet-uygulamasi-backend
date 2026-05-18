<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

     'paths' => ['api/*', 'sanctum/csrf-cookie'], // API yolları için izin ver
'allowed_methods' => ['*'],                  // Tüm HTTP metodlarına izin ver
'allowed_origins' => [
    'http://localhost:3000', // Add this
    'http://localhost:3001',
], // Belirtilen origin'den gelen istekleri kabul et
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],                  // Tüm başlıklara izin ver
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,              // Credentials (cookie, oturum) gönderimine izin ver
  'broadcasting/auth',    
// Eğer frontend ile kimlik doğrulama yapıyorsan true yap


];