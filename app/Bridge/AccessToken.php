<?php

namespace App\Bridge;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKeyInterface;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;

class AccessToken extends PassportAccessToken
{
    private ?CryptKeyInterface $capturedPrivateKey = null;

    public function setPrivateKey(CryptKeyInterface $privateKey): void
    {
        parent::setPrivateKey($privateKey);
        $this->capturedPrivateKey = $privateKey;
    }

    public function toString(): string
    {
        $userId = $this->getUserIdentifier();

        $user = $userId !== null
            ? \App\Models\User::find($userId)
            : null;

        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText(
                $this->capturedPrivateKey->getKeyContents(),
                $this->capturedPrivateKey->getPassPhrase() ?? ''
            ),
            InMemory::plainText('empty', 'empty')
        );

        $builder = $config->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('client_id', $this->getClient()->getIdentifier());

        if ($user !== null) {
            $builder = $builder
                ->withClaim('name', $user->name ?? '')
                ->withClaim('email', $user->email ?? '');
        }

        return $builder
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
}