<?php

declare(strict_types=1);

namespace Domain\Authentication\Entity;

use Domain\Authentication\ValueObject\Role;
use Domain\Shared\Entity\{IdentityTrait, TimestampTrait};
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @TODO find a way to remove symfony interfaces from the domain model user
 * Class User
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentityTrait;
    use TimestampTrait;

    private ?string $username = null;

    private ?string $job_title = null;

    private ?string $gender = 'M';

    private ?string $email = null;

    private array $roles = [Role::USER];

    private ?string $password = null;

    private ?\DateTimeImmutable $last_login_at = null;

    private ?string $last_login_ip = null;

    public static function createBasicWithRequiredFields(
        string $username,
        string $email,
        string $password,
        bool $is_admin = false
    ): self {
        return (new self())
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword($password)
            ->setRoles([$is_admin ? Role::ADMIN : Role::USER]);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->job_title;
    }

    public function setJobTitle(?string $job_title): self
    {
        $this->job_title = $job_title;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = Role::USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->last_login_at;
    }

    public function setLastLoginAt(?\DateTimeInterface $last_login_at): self
    {
        if (null !== $last_login_at) {
            $this->last_login_at = \DateTimeImmutable::createFromInterface($last_login_at);
        } else {
            $this->last_login_at = null;
        }

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->last_login_ip;
    }

    public function setLastLoginIp(?string $last_login_ip): self
    {
        $this->last_login_ip = $last_login_ip;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getEmail();
    }
}
