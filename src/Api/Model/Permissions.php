<?php

namespace Promenade\Interview\Api\Model;

use Psr\Http\Message\ServerRequestInterface;

class Permissions
{
    public const USER_GROUP_HEADER = 'user-group';

    public const USER_GROUP_GUEST           = 'guest';
    public const USER_GROUP_AUTHENTICATED   = 'authenticated';

    public const CANDIDATE_CREATE   = 'create_candidate';
    public const CANDIDATE_GET      = 'get_candidate';
    public const CANDIDATE_LIST     = 'list_candidates';
    public const CANDIDATE_UPDATE   = 'update_candidate';
    public const CANDIDATE_DELETE   = 'delete_candidate';

    private string $userGroup = self::USER_GROUP_GUEST;

    private array $acl = [
        self::USER_GROUP_GUEST => [
            self::CANDIDATE_CREATE      => false,
            self::CANDIDATE_GET         => false,
            self::CANDIDATE_LIST        => false,
            self::CANDIDATE_UPDATE      => false,
            self::CANDIDATE_DELETE      => false,
        ],
        self::USER_GROUP_AUTHENTICATED => [
            self::CANDIDATE_CREATE      => true,
            self::CANDIDATE_GET         => true,
            self::CANDIDATE_LIST        => true,
            self::CANDIDATE_UPDATE      => true,
            self::CANDIDATE_DELETE      => true,
        ],
    ];

    public function __construct(ServerRequestInterface $request)
    {
        // This is hacky but we're doing it for the sake of expediency and to lessen complexity for the candidate

        $this->request = $request;
        $userGroupHeader = $request->getHeader(self::USER_GROUP_HEADER);
        if (!empty($userGroupHeader) && !empty($this->acl[$userGroupHeader[0]])) {
            $this->userGroup = $userGroupHeader[0];
        }
    }

    public function can(string $action, ?object $object = null): bool
    {
        return $this->acl[$this->userGroup][$action] ?? false;
    }
}
