<?php

namespace Promenade\Interview\CandidateApi\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Promenade\Interview\Api\Controller\AbstractAction;
use Promenade\Interview\Api\Controller\Action;
use Promenade\Interview\Api\Model\Permissions;
use Promenade\Interview\Candidate\Model\CandidateNotCreatedException;
use Promenade\Interview\Candidate\Model\CandidateRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Class CreateCandidate
 * @package Promenade\Interview\CandidateApi\Controller
 */
class CreateCandidate extends AbstractAction
{
    private CandidateRepository $candidateRepository;
    private Permissions $permissions;

    /**
     * CreateCandidate constructor.
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
        if (!$this->permissions->can(Permissions::CANDIDATE_CREATE)) {
            return $this->respondWithData(null, StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $formData = $this->getFormData();
        $firstName = $formData->first_name;
        $lastName = $formData->last_name;
        $email = $formData->email;

        try {
            $nameValidator = v::alnum()->length(1);
            $nameValidator->assert($firstName);
            $nameValidator->assert($lastName);
            v::email()->assert($email);
        } catch (NestedValidationException $exception) {
            return $this->respondWithData([
                'error' => $exception->getMessage() . $exception->getFullMessage(),
                'context' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        try {
            $user = $this->candidateRepository->createCandidate($firstName, $lastName, $email);
            return $this->respondWithData($user, StatusCodeInterface::STATUS_CREATED);
        } catch (CandidateNotCreatedException $e) {
            $this->logger->error("CreateCandidate action CandidateNotCreatedException: " . $e->getCode() . " " .$e->getMessage());

            return $this->respondWithData([
                'error' => 'Candidate not created ' . $e->getMessage(),
                'context' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (\PDOException $e) {
            $this->logger->error("CreateCandidate action PDOException: " . $e->getCode() . " " .$e->getMessage());

            return $this->respondWithData([
                'error' => 'Internal Server Error',
                'context' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
