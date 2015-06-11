<?php
use Silex\Provider\MonologServiceProvider;

// include the prod configuration
require __DIR__ . '/production.php';

// enable the debug mode
$app['debug'] = true;
$app['config'] = array(
    'database' => array(
        'driver'   => 'pdo_mysql',
        'dbname'   => 'meuguru_web',
        'host'     => '177.54.144.49',
        'user'     => 'meuguru_abril',
        'password' => 'Nova2015GuruK2',
        'charset'  => 'UTF8',
        'port'     => 3306
    )
);
