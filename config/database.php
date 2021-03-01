<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' =>  env('DB_HOST_READ', false) == env('DB_HOST_Write', false) ? [
            'driver' => 'mysql',
            'host' => env('DB_HOST', env('DB_HOST_Write','127.0.0.1')),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'ttpush'),
            'username' => env('DB_USERNAME', 'msg'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
            'timezone'=>'+8:00'
        ] : [
            'read' => [
                'host' => env('DB_HOST_READ', '127.0.0.1')
            ],
            'write' => [
                'host' => env('DB_HOST_WRITE', '127.0.0.1')
            ],
            'driver' => 'mysql',
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'ttpush'),
            'username' => env('DB_USERNAME', 'msg'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
            'timezone'=>'+8:00'
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'mssqlTcc' => [
            'driver' => 'sqlsrv',
            'host' => '192.168.6.59',
            'port' => 1433,
            'database' => 'tcc',
            'username' => 'ediuser',
            'password' => 'aa1234$$',
            'charset' => '',
            'prefix' => '',
        ],

        'mysqlTT' => [
            'driver' => 'mysql',
            'host' => '163.29.101.14',
            'port' =>'3306',
            'database' => 'tt003',
            'username' => 'tt003',
            'password' => '2101me',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
            'timezone' => '+0:00'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'default' => [
            'host' => env('REDIS_HOST', 'temp-ttpush.7wqkr2.0001.apn2.cache.amazonaws.com'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

        'session' => [
            'host'     => env('REDIS_HOST', 'temp-ttpush.7wqkr2.0001.apn2.cache.amazonaws.com'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
