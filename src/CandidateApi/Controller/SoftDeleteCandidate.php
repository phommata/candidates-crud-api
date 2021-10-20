<?php

namespace Promenade\Interview\CandidateApi\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Controller\AbstractAction;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;
use Promenade\Interview\Candidate\Model\CandidateNotUpdatedException;
use Promenade\Interview\Candidate\Model\CandidateRepository;
use Psr\Http\Message\ResponseInterface as Response;


/**
 * Class SoftDeleteCandidate
 * @package Promenade\Interview\CandidateApi\Controller
 */
class SoftDeleteCandidate extends AbstractAction
{
    private CandidateRepository $candidateRepository;
    private Permissions $permissions;

    /**
     * SoftDeleteCandidate constructor.
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
        if (!$this->permissions->can(Permissions::CANDIDATE_UPDATE)) {
            return $this->respondWithData(null, StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $candidateId = $this->resolveArg('id');

        try {
            $this->candidateRepository->softDeleteCandidate($candidateId);
            return $this->respondWithData();
        } catch (CandidateNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Candidate not found',
                'context' => [
                    'candidate_id' => $candidateId,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (CandidateNotUpdatedException | CandidateNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Candidate not deleted',
                'context' => [
                    'candidate_id' => $candidateId,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
