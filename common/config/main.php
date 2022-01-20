<?php
return [
    'language' => 'ru',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'common\components\rbac\AuthManager',
            'itemFile' => '@common/components/rbac/items/items.php',
            'assignmentFile' => '@common/components/rbac/items/assignments.php',
            'ruleFile' => '@common/components/rbac/items/rules.php',
        ],
        'formatter' => [
            'class'         => 'yii\i18n\Formatter',
            'timeZone'      => 'UTC',
        ],

        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'ssl://smtp.yandex.ru',
                'username' => 'robot@pillikan.kz',//todo поменять электронный адрес
                'password' => 'Ethu2gai1o',
                'port' => '465',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' => 'mailer',
                    'levels' => ['error'],
                    'message' => [
                        'from' => ['robot@pillikan.kz' => 'Fantasy World'],
                        'to' => ['errors@pillikan.kz'],//todo поменять электронный адрес
                        'subject' => 'Ошибка на сайте',
                    ],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AAAAt9Pc35s:APA91bG-6qmANAfB36TzBVukQiZvajbXjY_p9aBSYoWtLsP77lw1YDoGJr1WQyYIEVOhnis3k6Pp9NWSlKch0b5-7-pfo3y2Cf4hC6vBM4XLnacPAPXv3SJBis9RU9U9gYCKHGh2VNfs',
        ],
    ],
];
