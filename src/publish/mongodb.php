<?php
return [

    'default' => [
        'host'     => [
            '127.0.0.1',
        ],
        'port'     => 27017,
        'database' => 'test',
        'username' => 'user_dev',
        'password' => '123456',
        'options'  => [
            'database' => 'admin',
        ],
        'pool'     => [
            'min_connections' => 10,
            'max_connections' => 100,
            'connect_timeout' => 10.0,
            'wait_timeout'    => 3.0,// 90
            'heartbeat'       => -1,
            'max_idle_time'   => (float)env('DB_MAX_IDLE_TIME', 60),
        ],
    ],


];