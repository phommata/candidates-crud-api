<?php

namespace Promenade\Interview\CandidateApi\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Api\Tests\AppTestTrait;
use Promenade\Interview\Candidate\Model\Candidate;
use Promenade\Interview\Api\Tests\TestCase;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;


class GetCandidateTest extends TestCase
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

        $this->assertSame(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetCandidate(): void
    {
        $candidateArray = [
            'id'            => '1',
            'first_name'    => 'John',
            'last_name'     => 'Jameson',
            'email'         => 'john@jameson.com',
            'created_at'    => '2020-07-01 12:33:04',
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate/1', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED]);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        $this->validateJsonData($candidateArray);
    }

    public function testGetCandidateNotFound(): void
    {
        $candidateArray = [
            'error'      => 'Candidate not found',
            'context'    => [
                'candidate_id' => '10',
            ]
        ];

        $this->mockCandidateRepo([]);

        $request = $this->createRequest('GET', '/candidate/10', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED]);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_NOT_FOUND, 'data' => $candidateArray], $response);
    }

    private function mockCandidateRepo(array $data)
    {
        if (empty($data)) {
            $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
                ->method('getCandidateById')
                ->willThrowException(new CandidateNotFoundException());
        } else {
            $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
                ->method('getCandidateById')
                ->willReturn(new Candidate($data));
        }
    }
}
