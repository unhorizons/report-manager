<?php

declare(strict_types=1);

namespace Domain\Notification\Entity;

use Domain\Authentication\Entity\User;
use Domain\Notification\ValueObject\PushSubscriptionKeys;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;

/**
 * Class PushSubscription.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class PushSubscription
{
    use IdentityTrait;
    use TimestampTrait;

    private ?string $endpoint = null;
    private ?string $expiration_time = null;
    private ?PushSubscriptionKeys $keys = null;
    private ?User $user = null;

    public static function fromArray(array $data, User $user): self
    {
        return (new self())
            ->setEndpoint($data['endpoint'])
            ->setExpirationTime($data['expiration_time'] ?? null)
            ->setKeys(PushSubscriptionKeys::fromArray($data['keys']))
            ->setUser($user);
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getExpirationTime(): ?string
    {
        return $this->expiration_time;
    }

    public function setExpirationTime(?string $expiration_time): self
    {
        $this->expiration_time = $expiration_time;

        return $this;
    }

    public function getKeys(): ?PushSubscriptionKeys
    {
        return $this->keys;
    }

    public function setKeys(?PushSubscriptionKeys $keys): self
    {
        $this->keys = $keys;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
