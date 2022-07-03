<?php

declare(strict_types=1);

namespace Application\Authentication\Command;

use Domain\Authentication\Entity\User;
use Domain\Authentication\ValueObject\Gender;
use Domain\Authentication\ValueObject\Roles;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * class UpdateUserCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateUserCommand
{
    public Gender $gender;
    public Roles $roles;

    public function __construct(
        public User $user,

        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotBlank]
        public ?string $username = null,

        #[Assert\NotBlank]
        public ?string $job_title = null,
    ) {
        $this->email = $this->user->getEmail();
        $this->username = $this->user->getUsername();
        $this->job_title = $this->user->getJobTitle();
        $this->gender = $this->user->getGender();
        $this->roles = Roles::fromArray($this->user->getRoles());
    }
}
