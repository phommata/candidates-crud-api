<?php

namespace Promenade\Interview\Api\Tests;

use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Swaggest\JsonSchema\Schema;

/**
 * App Test Trait.
 */
trait AppTestTrait
{
    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }

    /**
     * Create a server request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array $serverParams The server parameters
     *
     * @return ServerRequestInterface
     */
//    protected function createRequest(
//        string $method,
//        $uri,
//        array $serverParams = []
//    ): ServerRequestInterface {
//        return (new ServerRequestFactory())->createServerRequest($method, $uri, $serverParams);
//    }

    /**
     * Create a JSON request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array|null $data The json data
     *
     * @return ServerRequestInterface
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Verify that the given array is an exact match for the JSON returned.
     *
     * @param array $expected The expected array
     * @param ResponseInterface $response The response
     *
     * @throws JsonException
     * @return void
     */
    protected function assertJsonData(array $expected, ResponseInterface $response): void
    {
        $actual = (string)$response->getBody();
        $this->assertSame($expected, (array)json_decode($actual, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Validate JSON data
     *
     * @param array $candidateArray
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    protected function validateJsonData(array $candidateArray) : void
    {
        $json = file_get_contents(__DIR__ . '/../../CandidateApi/Model/GetResponseSchema.json');
        $schema = Schema::import(json_decode($json));
        $schema->in(json_decode(json_encode($candidateArray)));
    }
}
