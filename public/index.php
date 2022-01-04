<?php

require '../vendor/autoload.php';

$container = new \DI\Container();

\Slim\Factory\AppFactory::setContainer($container);
$app = \Slim\Factory\AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require '../app/container.php';

require '../app/routes.php';

$app->run();
