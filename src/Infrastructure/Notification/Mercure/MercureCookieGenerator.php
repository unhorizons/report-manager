<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Mercure;

use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class MercureCookieGenerator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class MercureCookieGenerator
{
    public function __construct(
        private readonly string $secret,
        private readonly NotificationService $service
    ) {
    }

    public function generate(User $user): Cookie
    {
        if (strlen($this->secret) > 0) {
            $channels = array_map(
                fn (string $channel) => "/notifications/${channel}",
                $this->service->getChannelsForUser($user)
            );
            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText($this->secret)
            );
            $token = $config->builder()
                ->withClaim('mercure', [
                    'subscribe' => $channels,
                ])
                ->getToken($config->signer(), $config->signingKey())
                ->toString();

            return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
        }

        throw new \RuntimeException('empty secret token given !');
    }
}
