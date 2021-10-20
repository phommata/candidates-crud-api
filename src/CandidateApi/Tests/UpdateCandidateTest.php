<?php

namespace Promenade\Interview\CandidateApi\Tests;

use Promenade\Interview\Api\Tests\AppTestTrait;
use Promenade\Interview\Candidate\Model\Candidate;
use Promenade\Interview\Api\Tests\TestCase;


class UpdateCandidateTest extends TestCase
{
    use AppTestTrait;

    public function setUp(): void
    {
        $this->getAppInstance();
    }

    public function testNonAuthenticatedRequest(): void
    {
        $this->mockCandidateRepo([]);

        $this->createRequest('GET', '/candidate/1');
        $request = $this->createRequest('GET', '/candidate/1');
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(403, $response->getStatusCode());
    }

    private function mockCandidateRepo(array $data)
    {
        $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
            ->method('updateCandidate')
            ->willReturn(new Candidate($data));
    }
}
