<?php

namespace Promenade\Interview\Candidate\Model;

class Candidate implements \JsonSerializable
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /*
     * What other getters/setters would you put here?
     * Flesh out this
     */

    public function getFirstName(): ?string
    {
        return $this->data['first_name'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->data['last_name'] ?? null;
    }

    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
