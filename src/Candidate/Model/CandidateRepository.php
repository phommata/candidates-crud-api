<?php

namespace Promenade\Interview\Candidate\Model;

use Monolog\Logger;

class CandidateRepository
{
    private \PDO $db;
    private CandidateFactory $candidateFactory;
    protected Logger $logger;

    public function __construct(
        \PDO $db,
        CandidateFactory $candidateFactory,
        Logger $logger
    ) {
        $this->db = $db;
        $this->candidateFactory = $candidateFactory;
        $this->logger = $logger;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string $sort
     * @param string $dir
     * @return array
     * @throws CandidateNotFoundException
     */
    public function getAllCandidates(?string $fromDate, ?string $toDate, ?string $sort, ?string $dir): array
    {
        $sql = 'SELECT id, first_name, last_name, email, DATE_FORMAT(created_at,"%Y-%m-%dT%H:%i:%s") as created_at
                FROM candidates';

        $whereBool = false;

        if (!empty($fromDate)) {
            $sql .= " WHERE created_at >= '$fromDate'";
            $whereBool = true;
        }

        if (!empty($toDate)) {
            $sql .= $whereBool ? ' AND' : ' WHERE';
            $sql .= " created_at <= '$toDate'";
            $whereBool = true;
        }

        $sql .= $whereBool ? ' AND' : ' WHERE';
        $sql .= " active = 1";

        if (!empty($sort) && !(empty($dir))) {
            $sql .= " ORDER BY $sort $dir";
        }

        $sth = $this->db->prepare($sql);

        $sth->execute([]);

        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($results)) {
            throw new CandidateNotFoundException();
        }

        return $results;
    }

    /**
     * @param string $candidateId
     * @return Candidate
     * @throws CandidateFactoryCreateException
     */
    public function getCandidateById(string $candidateId): Candidate
    {
        $sth = $this->db->prepare('
            SELECT id, first_name, last_name, email, DATE_FORMAT(created_at,"%Y-%m-%dT%H:%i:%s") as created_at
            FROM candidates
            WHERE id = :id
            AND active = 1
        ');

        $sth->execute(['id' => $candidateId]);
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new CandidateNotFoundException();
        }

        return $this->candidateFactory->create($result);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @return Candidate
     * @throws CandidateFactoryCreateException | \PDOException
     */
    public function createCandidate(string $firstName, string $lastName, string $email): Candidate
    {
        try {
            $sth = $this->db->prepare('
                INSERT INTO candidates (first_name, last_name, email) 
                VALUES
                    (:first_name, :last_name, :email) 
            ');

            $result = $sth->execute([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email
            ]);
        } catch (\PDOException $exception) {
            $this->logger->error("CandidateRepository createCandidate PDOException: " . $exception->getCode() . " " .$exception->getMessage());
            throw $exception;
        }

        if (empty($result)) {
            throw new CandidateNotCreatedException();
        }

        return $this->getCandidateById($this->db->lastInsertId());
    }

    /**
     * @param string $candidate_id
     * @param string|null $first_name
     * @param string|null $last_name
     * @param string|null $email
     * @return Candidate
     * @throws CandidateFactoryCreateException
     */
    public function updateCandidate(string $candidate_id, ?string $first_name, ?string $last_name, ?string $email): Candidate
    {
        try {
            $this->getCandidateById($candidate_id);
        } catch (CandidateNotFoundException $exception) {
            throw $exception;
        }

        $sql = '
            UPDATE candidates 
            SET';
        $executeArgs = [];

        $updateArgs = ['first_name', 'last_name', 'email'];
        $argSet = false;
        $count = 0;

        for ($i = 0; $i < count($updateArgs); $i++) {
            if (!empty(${$updateArgs[$i]})) {
                $argSet = true;

                if ($argSet && $count > 0) {
                    $sql .= ',';
                }

                $sql .= " $updateArgs[$i] = :$updateArgs[$i]";
                $executeArgs[$updateArgs[$i]] = ${$updateArgs[$i]};
                $count++;
            }
        }

        $sql .= ' WHERE id = :id ';
        $executeArgs['id'] = $candidate_id;

        $sth = $this->db->prepare($sql);

        $result = $sth->execute($executeArgs);

        if (empty($result)) {
            throw new CandidateNotUpdatedException();
        }

        return $this->getCandidateById($candidate_id);
    }

    /**
     * @param string $candidateId
     * @return bool
     * @throws CandidateFactoryCreateException
     */
    public function softDeleteCandidate(string $candidateId): bool
    {
        try {
            $this->getCandidateById($candidateId);
        } catch (CandidateNotFoundException $exception) {
            throw $exception;
        }
        $sth = $this->db->prepare('
            UPDATE candidates 
            SET 
                active = 0
            WHERE id = :id 
        ');

        $sth->execute(['id' => $candidateId]);
        $result = $sth->execute();

        if (empty($result)) {
            throw new CandidateNotSoftDeletedException();
        }

        return $result;
    }
}
