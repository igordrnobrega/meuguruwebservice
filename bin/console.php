<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Application as SymfonyApplication;

$input = new ArgvInput();
$app   = require __DIR__ . '/../src/app.php';

$env = $input->getParameterOption(array('--env', '-e'), getenv('APPLICATION_ENV') ? : 'production');

require __DIR__ . '/../config/' . $env . '.php';

$cli = new SymfonyApplication('Load CSV to Database', '1.0');
$cli->addCommands(array(
    new App\Console\Command\LoadCsvCommand($app)
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));
$cli->run();
