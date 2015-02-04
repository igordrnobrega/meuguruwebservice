<?php
use Silex\Provider\MonologServiceProvider;

// include the prod configuration
require __DIR__ . '/production.php';

// enable the debug mode
$app['debug'] = true;
$app['config'] = array(
    'database' => array(
        'driver'   => 'pdo_mysql',
        'dbname'   => 'meuguru_dev',
        'host'     => '177.54.144.49',
        'user'     => 'meuguru_guru',
        'password' => 'GURU@K2@mySQL',
        'charset'  => 'UTF8',
        'port'     => 3306
    )
);
