<?php
// configure your app for the production environment
$app['twig.path'] = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');
$app['data.path'] = __DIR__ . '/../var/data';

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
