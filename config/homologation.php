<?php
use Silex\Provider\MonologServiceProvider;

// include the prod configuration
require __DIR__ . '/production.php';

// enable the debug mode
$app['debug'] = true;
$app['config'] = array(
    'database' => array(
        'driver'   => 'pdo_mysql',
        'dbname'   => '',
        'host'     => '',
        'user'     => '',
        'password' => '',
        'charset'  => 'UTF8',
        'port'     => 3306
    )
);
