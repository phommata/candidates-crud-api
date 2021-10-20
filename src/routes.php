<?php
declare(strict_types=1);

use Promenade\Interview\CandidateApi\Controller\{GetCandidate, ListCandidates, CreateCandidate, UpdateCandidate, SoftDeleteCandidate};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app): void {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    // If you don't supply /candidate, you will get the readme.
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(file_get_contents(__DIR__ . '/../README.md'));
        return $response;
    });

    /*
     * This is where the app routes should go
     * Use the standard HTTP methods as the $group methods
     */
    $app->group('/candidate', function (Group $group) {
        $group->get('', ListCandidates::class);
        $group->get('/{id}', GetCandidate::class);
        $group->post('', CreateCandidate::class);
        $group->patch('/{id}', UpdateCandidate::class);
        $group->delete('/{id}', SoftDeleteCandidate::class);
    });
};
