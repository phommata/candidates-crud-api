<?php

namespace Promenade\Interview\CandidateApi\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Controller\AbstractAction;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;
use Promenade\Interview\Candidate\Model\CandidateRepository;
use Promenade\Interview\Candidate\Model\CandidateSortException;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Class ListCandidates
 * @package Promenade\Interview\CandidateApi\Controller
 */
class ListCandidates extends AbstractAction
{
    private CandidateRepository $candidateRepository;
    private Permissions $permissions;

    /**
     * ListCandidates constructor.
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
     */
    protected function action(): Response
    {
        $queryParams = $this->request->getQueryParams();
        $fromDate = $queryParams['from-date'] ?? null;
        $toDate = $queryParams['to-date'] ?? null;
        $sort = $queryParams['sort'] ? strtolower($queryParams['sort']) : null;
        $dir = $queryParams['dir'] ? strtolower($queryParams['dir']) : null;

        if (!$this->permissions->can(Permissions::CANDIDATE_GET)) {
            return $this->respondWithData(null, StatusCodeInterface::STATUS_FORBIDDEN);
        }

        try {
            if (!empty($fromDate) && empty($toDate)) {
                v::dateTime('Y-m-d\TH:i:s')->assert($fromDate);
            } else if (!empty($fromDate) && !empty($toDate)) {
                v::dateTime('Y-m-d\TH:i:s')->max($toDate)->assert($fromDate);
            }

            if (!empty($toDate) && empty($fromDate)) {
                v::dateTime('Y-m-d\TH:i:s')->assert($toDate);
            }

            if (empty($sort) && !empty($dir)) {
                throw new CandidateSortException('sort - must only be (id, first_name, last_name, created_at)');
            } else if (!empty($sort) && empty($dir)) {
                throw new CandidateSortException('dir - must only be (asc, desc)');
            } else if (!empty($sort) && !empty($dir)) {
                if (!in_array($sort, ['id', 'first_name', 'last_name', 'email', 'created_at'])) throw new CandidateSortException($sort . ' - must only be (id, first_name, last_name, created_at)');
                if (!in_array($dir, ['desc', 'asc'])) throw new CandidateSortException($dir . ' - must only be (asc, desc)');
            }
        } catch (NestedValidationException $exception) {
            return $this->respondWithData([
                'error' => $exception->getMessage() . $exception->getFullMessage(),
                'context' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'sort' => $sort,
                    'dir' => $dir,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (CandidateSortException $exception) {
            return $this->respondWithData([
                'error' => 'These rules must pass for ' . $exception->getMessage(),
                'context' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'sort' => $sort,
                    'dir' => $dir,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        try {
            $candidates = $this->candidateRepository->getAllCandidates($fromDate, $toDate, $sort, $dir);
            return $this->respondWithData($candidates);
        } catch (CandidateNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Candidate(s) not found',
                'context' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'sort' => $sort,
                    'dir' => $dir,
                ],
            ], StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }
}
