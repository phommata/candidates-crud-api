<?php

namespace Promenade\Interview\CandidateApi\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Controller\AbstractAction;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Candidate\Model\CandidateNotFoundException;
use Promenade\Interview\Candidate\Model\CandidateNotUpdatedException;
use Promenade\Interview\Candidate\Model\CandidateRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


/**
 * Class UpdateCandidate
 * @package Promenade\Interview\CandidateApi\Controller
 */
class UpdateCandidate extends AbstractAction
{
    private CandidateRepository $candidateRepository;
    private Permissions $permissions;

    /**
     * UpdateCandidate constructor.
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

        $formData = $this->getFormData();
        $firstName = $formData->first_name ?? null;
        $lastName = $formData->last_name ?? null;
        $email = $formData->email ?? null;

        try {
            $nameValidator = v::length(1);
            $nameValidator->assert($firstName);
            $nameValidator->assert($lastName);
            v::email()->assert($email);
        } catch (NestedValidationException $exception) {
            return $this->respondWithData([
                'error' => $exception->getMessage() . $exception->getFullMessage(),
                'context' => [
                    'candidate_id' => $candidateId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        try {
            $user = $this->candidateRepository->updateCandidate($candidateId, $firstName, $lastName, $email);
            return $this->respondWithData($user);
        } catch (CandidateNotUpdatedException $e) {
            return $this->respondWithData([
                'error' => 'Candidate not updated',
                'context' => [
                    'candidate_id' => $candidateId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (CandidateNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Candidate not found',
                'context' => [
                    'candidate_id' => $candidateId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }
}
