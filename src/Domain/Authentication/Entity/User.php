<?php

declare(strict_types=1);

namespace Domain\Authentication\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Domain\Authentication\ValueObject\Gender;
use Domain\Authentication\ValueObject\Roles;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Domain\Shared\Entity\{IdentityTrait, TimestampTrait};
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @TODO find a way to remove symfony interfaces from the domain model user
 * Class User
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    use IdentityTrait;
    use TimestampTrait;

    private ?string $username = null;

    private ?string $job_title = null;

    private Gender $gender;

    private ?string $email = null;

    private Roles $roles;

    private ?string $password = null;

    private ?\DateTimeImmutable $last_login_at = null;

    private ?string $last_login_ip = null;

    /**
     * @var Collection<Report>
     */
    private Collection $submitted_reports;

    /**
     * @var Collection<Report>
     */
    private Collection $assigned_reports;

    /**
     * @var Collection<Evaluation>
     */
    private Collection $evaluations;

    public function __construct()
    {
        $this->gender = Gender::male();
        $this->roles = Roles::employee();
        $this->evaluations = new ArrayCollection();
        $this->submitted_reports = new ArrayCollection();
        $this->assigned_reports = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->username;
    }

    public static function create(
        string $username,
        string $email,
        string $password,
        bool $is_admin = false
    ): self {
        return (new self())
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword($password)
            ->setRoles($is_admin ? Roles::superAdmin() : Roles::employee());
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

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender|string $gender): self
    {
        if ($gender instanceof Gender) {
            $this->gender = $gender;
        } else {
            $this->gender = Gender::fromString($gender);
        }

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
        return $this->roles->toArray();
    }

    public function setRoles(Roles|array $roles): self
    {
        if ($roles instanceof Roles) {
            $this->roles = $roles;
        } else {
            $this->roles = Roles::fromArray($roles);
        }

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

    public function hasRole(string $role): bool
    {
        if ('ROLE_USER' === $role && 1 !== count($this->getRoles())) {
            return false;
        }

        return in_array($role, $this->getRoles(), true);
    }

    public function getSubmittedReports(): Collection
    {
        return $this->submitted_reports;
    }

    public function setSubmittedReports(Collection $reports): self
    {
        $this->submitted_reports = $reports;

        return $this;
    }

    public function getAssignedReports(): Collection
    {
        return $this->assigned_reports;
    }

    public function addAssignedReport(Report $report): self
    {
        if (! $this->assigned_reports->contains($report)) {
            $this->assigned_reports[] = $report;
            $report->addManager($this);
        }

        return $this;
    }

    public function removeAssignedReport(Report $report): self
    {
        if ($this->assigned_reports->removeElement($report)) {
            $report->removeManager($this);
        }

        return $this;
    }

    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function setEvaluations(Collection $evaluations): self
    {
        $this->evaluations = $evaluations;

        return $this;
    }

    public function getAutocompleteValue(): string
    {
        return (string) $this->id;
    }

    public function getAutocompleteLabel(): string
    {
        return (string) $this->username;
    }
}
