<?php

namespace App\Passport;

use App\Models\User;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use DateTimeImmutable;
use Lcobucci\JWT\Token;

class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait;

    private ?User $user = null;

    /**
     * Create a new token instance.
     *
     * @param  string  $userIdentifier
     * @param  array  $scopes
     * @param ClientEntityInterface $client
     * @return void
     */
    public function __construct($userIdentifier, array $scopes, ClientEntityInterface $client)
    {
        $this->setUserIdentifier($userIdentifier);
        $this->setUserByUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }

        $this->setClient($client);
    }

    /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('name', $this->getUser()?->name)
            ->withClaim('email', $this->getUser()?->email)
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    private function setUserByUserIdentifier($userIdentifier): static
    {
        $this->user = User::find($userIdentifier);

        return $this;
    }

    private function getUser(): ?User
    {
        return $this->user;
    }
}
