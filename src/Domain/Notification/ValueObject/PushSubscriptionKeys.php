<?php

declare(strict_types=1);

namespace Domain\Notification\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class PushSubscriptionKeys.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class PushSubscriptionKeys
{
    public readonly string $p256dh;
    public readonly string $auth;

    private function __construct($p256dh, $auth)
    {
        Assert::notEmpty($p256dh);
        Assert::notEmpty($auth);

        $this->p256dh = $p256dh;
        $this->auth = $auth;
    }

    public static function fromArray(array $keys): self
    {
        Assert::keyExists($keys, 'p256dh');
        Assert::keyExists($keys, 'auth');

        return new self($keys['p256dh'], $keys['auth']);
    }

    public function toArray(): array
    {
        return [
            'auth' => $this->auth,
            'p256dh' => $this->p256dh
        ];
    }
}
