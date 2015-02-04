<?php
use Symfony\Component\Debug\Debug;

define('BASE_PATH', __DIR__ . '/../');

getenv('APPLICATION_ENV')
    ? define('APPLICATION_ENV', getenv('APPLICATION_ENV'))
    : define('APPLICATION_ENV', 'development');

require_once __DIR__ . '/../vendor/autoload.php';

if(APPLICATION_ENV === 'development') {
    Debug::enable();
}

$app = require __DIR__ . '/../src/app.php';
require __DIR__ . '/../config/' . APPLICATION_ENV . '.php';
require __DIR__ . '/../src/controllers.php';

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));

$app->run();