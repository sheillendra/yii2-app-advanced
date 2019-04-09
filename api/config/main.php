<?php

$params = array_merge(
        require __DIR__ . '/../../common/config/params.php'
        , require __DIR__ . '/../../common/config/params-local.php'
        , require __DIR__ . '/params.php'
        , require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\UserExt',
            'enableSession' => false,
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
            //'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user'
                ],
            ],
        ],
        'request' => [
            'class' => 'yii\web\Request',
            'csrfParam' => '_apiCSRF',
            'csrfCookie' => ['httpOnly' => true],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $responseHeaders = $response->getHeaders();
                $responseHeaders->set('Access-Control-Allow-Origin', '*');
            },
        ],
    ],
    'params' => $params,
];
