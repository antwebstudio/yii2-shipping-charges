<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
require_once dirname(__DIR__).'/vendor/yiisoft/yii2/Yii.php';

// Yii::setAlias('@common', dirname(dirname(__DIR__)).'/src/common');
// Yii::setAlias('@tests', dirname(__DIR__));

// $config = require dirname(__DIR__).'/config/unit.php';
$config = [
    'id' => 'application',
    'basePath' => __DIR__,
    'aliases' => [
        'common' => __DIR__,
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'test',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=easyorder',
            'username' => 'root',
            'password' => 'root',
            'tablePrefix' => '',
            'charset' => 'utf8',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'ant\model\user\User',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
    ],
];
new yii\web\Application($config);
