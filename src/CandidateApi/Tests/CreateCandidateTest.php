<?php

namespace Promenade\Interview\CandidateApi\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Tests\AppTestTrait;
use Promenade\Interview\Candidate\Model\Candidate;
use Promenade\Interview\Api\Tests\TestCase;


class CreateCandidateTest extends TestCase
{
    use AppTestTrait;

    public function setUp(): void
    {
        $this->getAppInstance();
    }

    public function testNonAuthenticatedRequest(): void
    {
        $this->mockCandidateRepo([]);

        $this->createRequest('POST', '/candidate');
        $request = $this->createJsonRequest('POST', '/candidate', []);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }

    private function mockCandidateRepo(array $data)
    {
        $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
            ->method('createCandidate')
            ->willReturn(new Candidate($data));
    }
}
