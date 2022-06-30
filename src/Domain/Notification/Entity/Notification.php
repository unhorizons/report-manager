<?php

declare(strict_types=1);

namespace Domain\Notification\Entity;

use Domain\Authentication\Entity\User;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Class Notification.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Notification implements \Stringable
{
    use IdentityTrait;
    use TimestampTrait;

    private Uuid $uuid;

    private ?User $user = null;

    private ?string $channel = 'public';

    private ?string $target = null;

    private ?string $url = null;

    private ?string $message = null;

    private bool $is_read = false;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function __toString(): string
    {
        return (string) json_encode([
            'uuid' => $this->uuid->toRfc4122(),
            'user' => (string) $this->getUser()?->getId(),
            'target' => (string) $this->getTarget(),
            'url' => (string) $this->getUrl(),
            'message' => (string) $this->getMessage(),
            'created_at' => (int) $this->getCreatedAt()?->getTimestamp(),
        ]);
    }

    public function isRead(): bool
    {
        return $this->getIsRead();
    }

    public function getUuid(): UuidV4|Uuid
    {
        return $this->uuid;
    }

    public function setUuid(UuidV4|Uuid $uuid): self
    {
        $this->uuid = $uuid;

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

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsRead(): bool
    {
        return $this->is_read;
    }

    public function setIsRead(?bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }
}
