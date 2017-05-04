<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache the HTML
    |--------------------------------------------------------------------------
    | You can Globally turn caching on and off for all widgets from here
    |
    */
    'enable_cache' => false,
    /*
    |--------------------------------------------------------------------------
    | Default Cache Lifetime
    |--------------------------------------------------------------------------
    | You Can Specify a fallback value for the cache lifetime of your
    | widgets, so you do not have to define the "public $cacheLifeTime"
    | on each and every widget class in your application.
    |
    */
    'default_cache_lifetime' => 1, //(minutes)
    /*
    |--------------------------------------------------------------------------
    | Minify Widget HTML
    |--------------------------------------------------------------------------
    | Minify widgets to save both 'cache storage space' and the 'page size'
    |
    | * Putting "APP_ENV=production" in .env will forcefully enable minification.
    |
    */
    'minify_html' => false,
    /*
    |--------------------------------------------------------------------------
    | Debug Mode for Widgets
    |--------------------------------------------------------------------------
    | It is helpful for you to see information about your widgets in the browser
    | as HTML title attribute.So it is recommended to turn it on for development.
    |
    | * Putting "APP_ENV=production" in .env will forcefully disable debug_info.
    |
    */
    'debug_info' => true,
];
