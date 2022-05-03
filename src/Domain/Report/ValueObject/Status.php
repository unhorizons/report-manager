<?php

declare(strict_types=1);

namespace Domain\Report\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class Status.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Status implements \Stringable
{
    public const STATUS = ['seen', 'unseen'];
    private readonly string $status;

    private function __construct(string $status)
    {
        Assert::notEmpty($status);
        Assert::inArray($status, self::STATUS);
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->status;
    }

    public function equals(self|string $status): bool
    {
        if ($status instanceof self) {
            return $this->status === $status->status;
        }

        return $this->status === $status;
    }

    public static function seen(): self
    {
        return new self('seen');
    }

    public static function unseen(): self
    {
        return new self('unseen');
    }

    public static function fromString(string $status): self
    {
        return new self($status);
    }
}
