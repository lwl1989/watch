<?php
return [
    // HTTP 請求的超時時間（秒）
    'timeout' => 5.0,

    // 預設發送配置
    'default' => [
        // 網關調用策略，預設：順序調用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 預設可用的發送網關
        'gateways' => [
            'Every8D',
            'Nexmo',
            'Twilio',
            'Mitake'//三竹簡訊
        ],
        'gateways_886'  =>  [
            'Mitake',
            'Every8D',
            'Nexmo',
            'Twilio',
        ]
    ],
    // 可用的網關配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],

        'Nexmo' => [
            'app_key' => env('NEXMO_KEY'),
            'app_secret' => env('NEXMO_SECRET'),
            'app_name'  =>  env('APP_NAME')
        ],
        'Twilio'=>[
            'app_key' => env('TWILIO_KEY'),
            'app_secret' => env('TWILIO_SECRET'),
            'from_mobile'=>env('TWILIO_MOBILE')
        ],
        'Every8D' => [
            'app_key' => env('EVERY8D_KEY'),
            'app_secret' => env('EVERY8D_SECRET'),
        ],
        'Mitake'=>[
            'app_key' => env('MITAKE_KEY'),
            'app_secret' => env('MITAKE_SECRET'),
        ]
    ]
];
