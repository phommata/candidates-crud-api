<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        \PDO::class => function (ContainerInterface $c) {
            return new \PDO('mysql:dbname=interview;host=db', 'root', 'interview');
        },

        \Promenade\Interview\Api\Model\Permissions::class => function (ContainerInterface $c) {
            return new \Promenade\Interview\Api\Model\Permissions($c->get(\Slim\Psr7\Request::class));
        },
    ]);
};
