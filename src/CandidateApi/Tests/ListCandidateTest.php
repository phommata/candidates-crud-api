<?php

namespace Promenade\Interview\CandidateApi\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Api\Tests\AppTestTrait;
use Promenade\Interview\Api\Tests\TestCase;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;
use Promenade\Interview\Candidate\Tests\FakeCandidateData;


class ListCandidateTest extends TestCase
{
    use AppTestTrait;

    public function setUp(): void
    {
        $this->getAppInstance();
    }

    public function testNonAuthenticatedRequest(): void
    {
        $this->mockCandidateRepo([]);

        $this->createRequest('GET', '/candidate');
        $request = $this->createRequest('GET', '/candidate');
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }


    public function testListCandidates(): void
    {
        $candidateArray = FakeCandidateData::DEFAULT_DATA;

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED]);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterFromDate(): void
    {
        $candidateArray = [
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ],
            [
                'id'            => '6',
                'first_name'    => 'David',
                'last_name'     => 'Yuengling',
                'email'         => 'dave@yuengling.drink',
                'created_at'    => '2020-07-06T05:33:10',
            ]
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(['from-date' => '2020-07-03T02:33:04']);

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterFromToDate(): void
    {
        $candidateArray = [
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ]
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'from-date' => '2020-07-03T02:33:04',
                    'to-date' => '2020-07-06T00:31:00'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterToDate(): void
    {
        $candidateArray = [
            [
                'id'            => '1',
                'first_name'    => 'John',
                'last_name'     => 'Jameson',
                'email'         => 'john@jameson.com',
                'created_at'    => '2020-07-01 12:33:04',
            ],
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ]
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'to-date' => '2020-07-06T00:31:00'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);
    }

    public function testListCandidatesFilterSortLastNameAsc(): void
    {
        $candidateArray = [
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '1',
                'first_name'    => 'John',
                'last_name'     => 'Jameson',
                'email'         => 'john@jameson.com',
                'created_at'    => '2020-07-01 12:33:04',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ],
            [
                'id'            => '6',
                'first_name'    => 'David',
                'last_name'     => 'Yuengling',
                'email'         => 'dave@yuengling.drink',
                'created_at'    => '2020-07-06T05:33:10',
            ]
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'sort' => 'last_name',
                    'dir' => 'asc'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterSortFirstNameDesc(): void
    {
        $candidateArray = [
            [
                'id'            => '6',
                'first_name'    => 'David',
                'last_name'     => 'Yuengling',
                'email'         => 'dave@yuengling.drink',
                'created_at'    => '2020-07-06T05:33:10',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ],
            [
                'id'            => '1',
                'first_name'    => 'John',
                'last_name'     => 'Jameson',
                'email'         => 'john@jameson.com',
                'created_at'    => '2020-07-01 12:33:04',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'sort' => 'first_name',
                    'dir' => 'desc'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterSortEmailAsc(): void
    {
        $candidateArray = [
            [
                'id'            => '6',
                'first_name'    => 'David',
                'last_name'     => 'Yuengling',
                'email'         => 'dave@yuengling.drink',
                'created_at'    => '2020-07-06T05:33:10',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ],
            [
                'id'            => '1',
                'first_name'    => 'John',
                'last_name'     => 'Jameson',
                'email'         => 'john@jameson.com',
                'created_at'    => '2020-07-01 12:33:04',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'sort' => 'email',
                    'dir' => 'asc'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterSortCreatedAtDesc(): void
    {
        $candidateArray = [
            [
                'id'            => '6',
                'first_name'    => 'David',
                'last_name'     => 'Yuengling',
                'email'         => 'dave@yuengling.drink',
                'created_at'    => '2020-07-06T05:33:10',
            ],
            [
                'id'            => '5',
                'first_name'    => 'Jacob',
                'last_name'     => 'Leinenkugel',
                'email'         => 'jake@line-en-ku-gul.co',
                'created_at'    => '2020-07-06T00:31:00',
            ],
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
            [
                'id'            => '2',
                'first_name'    => 'Samuel',
                'last_name'     => 'Adams',
                'email'         => 'sam@samadams.com',
                'created_at'    => '2020-07-03T02:33:04',
            ],
            [
                'id'            => '1',
                'first_name'    => 'John',
                'last_name'     => 'Jameson',
                'email'         => 'john@jameson.com',
                'created_at'    => '2020-07-01 12:33:04',
            ],
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'sort' => 'email',
                    'dir' => 'asc'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidatesFilterFromToDateSortCreatedAtDesc(): void
    {
        $candidateArray = [
            [
                'id'            => '4',
                'first_name'    => 'Jose',
                'last_name'     => 'Cuervo',
                'email'         => 'jose@cuervo.com',
                'created_at'    => '2020-07-05T22:12:45',
            ],
            [
                'id'            => '3',
                'first_name'    => 'Gerard',
                'last_name'     => 'Heineken',
                'email'         => 'gerry@heiny.com',
                'created_at'    => '2020-07-05T22:02:45',
            ],
        ];

        $this->mockCandidateRepo($candidateArray);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'from-date' => '2020-07-05T22:02:45',
                    'to-date' => '2020-07-05T22:12:45',
                    'sort' => 'created_at',
                    'dir' => 'desc'
                ]
            );

        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_OK, 'data' => $candidateArray], $response);

        array_walk($candidateArray, function($v) { $this->validateJsonData($v); });
    }

    public function testListCandidateFilterToDateNotFound(): void
    {
        $candidateArray = [
            'error'      => 'Candidate(s) not found',
            'context'    => [
                'from_date' => null,
                'to_date' => '2019-03-23T23:23:42',
                'sort' => null,
                'dir' => null,
            ]
        ];

        $this->mockCandidateRepo([], new CandidateNotFoundException());

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(['to-date' => '2019-03-23T23:23:42']);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_NOT_FOUND, 'data' => $candidateArray], $response);
    }

    public function testListCandidateFilterFromDateInvalid(): void
    {
        $candidateArray = [
            'error'      => 'All of the required rules must pass for "foobar"- "foobar" must be a valid date/time in the format "2005-12-30T01:02:03"',
            'context'    => [
                'from_date' => 'foobar',
                'to_date' => null,
                'sort' => null,
                'dir' => null,
            ]
        ];

        $this->mockCandidateRepo([]);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(['from-date' => 'foobar']);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_BAD_REQUEST, 'data' => $candidateArray], $response);
    }

    public function testListCandidateFilterToDateInvalid(): void
    {
        $candidateArray = [
            'error'      => 'All of the required rules must pass for "foobar"- "foobar" must be a valid date/time in the format "2005-12-30T01:02:03"',
            'context'    => [
                'from_date' => null,
                'to_date' => 'foobar',
                'sort' => null,
                'dir' => null,
            ]
        ];

        $this->mockCandidateRepo([]);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(['to-date' => 'foobar']);
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_BAD_REQUEST, 'data' => $candidateArray], $response);
    }

    public function testListCandidateSortDirMissing(): void
    {
        $candidateArray = [
            'error'      => 'These rules must pass for dir - must only be (asc, desc)',
            'context'    => [
                'from_date' => null,
                'to_date' => null,
                'sort' => 'id',
                'dir' => null,
            ]
        ];

        $this->mockCandidateRepo([]);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'sort' => 'id',
                ]
            );
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_BAD_REQUEST, 'data' => $candidateArray], $response);
    }

    public function testListCandidateSortMissingDir(): void
    {
        $candidateArray = [
            'error'      => 'These rules must pass for sort - must only be (id, first_name, last_name, created_at)',
            'context'    => [
                'from_date' => null,
                'to_date' => null,
                'sort' => null,
                'dir' => 'asc',
            ]
        ];

        $this->mockCandidateRepo([]);

        $request = $this->createRequest('GET', '/candidate', self::DEFAULT_HEADERS + ['user-group' => Permissions::USER_GROUP_AUTHENTICATED])
            ->withQueryParams(
                [
                    'dir' => 'asc',
                ]
            );
        $this->setPermissions($request);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonData(['statusCode' => StatusCodeInterface::STATUS_BAD_REQUEST, 'data' => $candidateArray], $response);
    }

    private function mockCandidateRepo(array $data, ?\Exception $e = null)
    {
        if (!empty($e)) {
            $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
                ->method('getAllCandidates')
                ->willThrowException($e);

        } else {
            $this->mock(\Promenade\Interview\Candidate\Model\CandidateRepository::class)
                ->method('getAllCandidates')
                ->willReturn($data);
        }
    }
}
