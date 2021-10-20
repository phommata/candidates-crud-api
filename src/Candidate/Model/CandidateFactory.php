<?php

namespace Promenade\Interview\Candidate\Model;

class CandidateFactory
{
    public function create(array $data): Candidate
    {
        try {
            return new Candidate($data);
        } catch (\Throwable $e) {
            throw new CandidateFactoryCreateException($e);
        }
    }
}
