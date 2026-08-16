<?php
// Path: config/app.php

declare(strict_types=1);


/**
 * Enterprise Application Configuration
 * Loads settings from environment variables via env() helper with fallback defaults.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    | This value is the name of your application, used in notifications and UI headers.
    */
    'name' => env('APP_NAME', 'Nour Trust ERP'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    | This value determines the "environment" your application is currently running in.
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    | When enabled, detailed error messages with stack traces will be shown.
    */
    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    | The base URL used for generating URLs across the system and links in emails.
    */
    'url' => env('APP_URL', 'https://nourtrust.com/ERP/public'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    | The default timezone for your application, used for datetime functions and GL logs.
    */
    'timezone' => env('APP_TIMEZONE', 'Asia/Riyadh'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale & Fallback
    |--------------------------------------------------------------------------
    | The default locale for translation files (e.g., 'ar' for Arabic, 'en' for English).
    */
    'locale' => env('APP_LOCALE', 'ar'),

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | System Version & Root Path
    |--------------------------------------------------------------------------
    | Internal system version and base directory location.
    */
    'version' => '2.0.0',

    'root' => dirname(__DIR__),
];