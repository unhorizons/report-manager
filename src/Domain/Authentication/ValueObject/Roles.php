<?php

declare(strict_types=1);

namespace Domain\Authentication\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class Role.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Roles implements \Stringable
{
    public const ROLES = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_REPORT_MANAGER'];
    public const ROLES_CHOICES = [
        'ROLE_ADMIN' => 'ROLE_ADMIN',
        'ROLE_USER' => 'ROLE_USER',
        'ROLE_REPORT_MANAGER' => 'ROLE_REPORT_MANAGER',
    ];

    private readonly array $roles;

    private function __construct(array $roles = ['ROLE_USER'])
    {
        Assert::notEmpty($roles, 'authentication.validations.empty_roles');
        foreach ($roles as $role) {
            Assert::inArray($role, self::ROLES, 'authentication.validations.invalid_roles');
        }

        $roles[] = 'ROLE_USER';
        $this->roles = array_unique($roles);
    }

    public function __toString(): string
    {
        return implode(',', $this->roles);
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public static function fromArray(array $roles): self
    {
        return new self($roles);
    }

    public static function admin(): self
    {
        return new self(['ROLE_ADMIN']);
    }

    public static function employee(): self
    {
        return new self();
    }

    public static function reportManager(): self
    {
        return new self(['ROLE_USER', 'ROLE_REPORT_MANAGER']);
    }

    public function equals(array|self $roles): bool
    {
        if ($roles instanceof self) {
            return $roles->roles === $this->roles;
        }

        return $roles === $this->roles;
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
