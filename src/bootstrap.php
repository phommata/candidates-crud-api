<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}

require ROOT_DIR . '/vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require ROOT_DIR . '/src/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require ROOT_DIR . '/src/dependencies.php';
$dependencies($containerBuilder);

//// Set up repositories
//$repositories = require ROOT_DIR . '/src/repositories.php';
//$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
//$middleware = require __DIR__ . '/src/middleware.php';
//$middleware($app);

// Register routes
$routes = require ROOT_DIR . '/src/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $container->get('settings')['displayErrorDetails'];

// Create Error Handler
//$responseFactory = $app->getResponseFactory();
//$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
//$errorHandler->forceContentType('application/json');

// Create Shutdown Handler
//$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
//register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
//$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
//$errorMiddleware->setDefaultErrorHandler($errorHandler);

return $app;
