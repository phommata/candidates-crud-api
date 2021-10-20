<?php
declare(strict_types=1);

namespace Promenade\Interview\Api\Tests;

use DI\Container;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Promenade\Interview\Api\Model\Permissions;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class TestCase extends PHPUnit_TestCase
{
    const DEFAULT_HEADERS = ['HTTP_ACCEPT' => 'application/json'];

    protected Container $container;
    protected App $app;

    /**
     * @throws Exception
     */
    protected function getAppInstance(): App
    {
        $this->app = require __DIR__ . '/../../bootstrap.php';
        $this->container = $this->app->getContainer();

        return $this->app;
    }

    protected function createRequest(
        string $method,
        string $path,
        array $headers = self::DEFAULT_HEADERS,
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    protected function setPermissions(SlimRequest $request): void
    {
        $this->container->set(Permissions::class, new Permissions($request));
    }
}
