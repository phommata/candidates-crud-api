<?php

namespace Promenade\Interview\CandidateApi\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Api\Tests\AppTestTrait;
use Promenade\Interview\Api\Tests\TestCase;


class SoftDeleteCandidateTest extends TestCase
{
    use AppTestTrait;

    public function setUp(): void
    {
        $this->getAppInstance();
    }

    public function testNonAuthenticatedRequest(): void
    {
        $this->mockCandidateRepo();

        $this->createRequest('DELETE', '/candidate/1');
        $request = $this->createRequest('DELETE', '/candidate/1');
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }


    public function testDeleteCandidate(): void
    {
        $this->mockCandidateRepo();

        $request = $this->createRequest('DELETE', '/candidate/1', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED]);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }


    private function mockCandidateRepo()
    {
        $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
            ->method('softDeleteCandidate')
            ->willReturn(true);
    }
}
