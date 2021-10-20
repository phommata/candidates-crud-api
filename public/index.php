<?php
declare(strict_types=1);

use Promenade\Interview\Api\Controller\ResponseEmitter;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;

define('ROOT_DIR', dirname(__DIR__));
require_once ROOT_DIR . '/src/bootstrap.php';

/** @var App $app */
$app = $GLOBALS['app'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$app->getContainer()->set(get_class($request), $request);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
