<?php

namespace App\Passport;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\AccessTokenRepository as Repository;

class AccessTokenRepository extends Repository
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessToken($userIdentifier, $scopes, $clientEntity);
    }
}
