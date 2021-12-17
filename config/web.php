<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        //'log',
    ],
    'language' => 'ru',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@orders' => '@app/modules/orders'
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'orders*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@orders/messages',
                ]
            ]
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => ['//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js']
                ]
            ],
        ],
        'formatter' => [
            'datetimeFormat' => 'php:Y-m-d H:i:s',
        ],
        'request' => [
            'cookieValidationKey' => 'Z08zOyjU2HQlGIxxTPOCnhRJEDYjHjUB',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET /orders' => 'orders/orders/index',
                'GET /orders/csv' => 'orders/orders/csv',
            ],
        ],
        'db' => $db,

    ],
    'modules' => [
        'orders' => [
            'class' => 'orders\Orders',
            'layout' => '@orders/views/layouts/main',
        ],
    ],
    'params' => [],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
