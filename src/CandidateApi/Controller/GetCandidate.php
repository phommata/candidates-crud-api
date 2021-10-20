<?php

namespace Promenade\Interview\CandidateApi\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Controller\AbstractAction;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;
use Promenade\Interview\Candidate\Model\CandidateRepository;
use Psr\Http\Message\ResponseInterface as Response;


/**
 * Class GetCandidate
 * @package Promenade\Interview\CandidateApi\Controller
 */
class GetCandidate extends AbstractAction
{
    private CandidateRepository $candidateRepository;
    private Permissions $permissions;

    /**
     * GetCandidate constructor.
     * @param CandidateRepository $candidateRepository
     * @param Permissions $permissions
     */
    public function __construct(CandidateRepository $candidateRepository, Permissions $permissions)
    {
        $this->candidateRepository = $candidateRepository;
        $this->permissions = $permissions;
    }

    /**
     * @return Response
     * @throws \Promenade\Interview\Candidate\Model\CandidateFactoryCreateException
     * @throws \Slim\Exception\HttpBadRequestException
     */
    protected function action(): Response
    {
        if (!$this->permissions->can(Permissions::CANDIDATE_GET)) {
            return $this->respondWithData(null, StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $candidateId = $this->resolveArg('id');

        try {
            $candidate = $this->candidateRepository->getCandidateById($candidateId);
            return $this->respondWithData($candidate);
        } catch (CandidateNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Candidate not found',
                'context' => [
                    'candidate_id' => $candidateId,
                ],
            ], StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }
}
