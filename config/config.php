<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    | The amount of time a user's permissions are stored in the cache in seconds.
    | The default value is 1 day (86400)
    */

    'cache_ttl' => 86400,

    /*
    |--------------------------------------------------------------------------
    | Cache Tagging
    |--------------------------------------------------------------------------
    | Boolean value if your cache supports tagging or not. A cache that
    | support tagging will allow you to flush a user's cached permissions
    | using the php artisan acl:clear command.
    */

    'cache_tagging' => true,
];
