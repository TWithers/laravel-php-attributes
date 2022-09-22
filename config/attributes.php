<?php

return [

    /**
     * Caching will make use of Laravel's built-in file caching. Using caching will be a massive performance benefit
     * as no directories and files need to be scanned for attributes and attribute usages
     */
    'use_cache' => true,

    /**
     * By default this will scan your listed directories below for all attributes and then search for them.
     *
     * If you want to avoid the initial search, you can list your attribute classes below:
     *     'App\Attributes\Foo',
     *     App\Attributes\Bar::class,
     *
     */
    'attributes' => [

    ],

    /**
     * By default this will scan all files inside your app folder for attributes.
     *
     * If you want to limit the folders, you can adjust the namespace and the files:
     * 'App\Http\Controllers' => app_path('Http/Controllers')
     */
    'directories' => [
        'App' => app_path(),
    ],

];
