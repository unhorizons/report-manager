<?php

declare(strict_types=1);

namespace Application\Authentication\Command;

use Domain\Authentication\ValueObject\Gender;
use Domain\Authentication\ValueObject\Roles;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * class CreateUserCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class RegisterUserCommand
{
    public Gender $gender;
    public Roles $roles;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,
        #[Assert\NotBlank]
        public ?string $username = null,
        #[Assert\NotBlank]
        public ?string $job_title = null,
    ) {
        $this->gender = Gender::male();
        $this->roles = Roles::employee();
    }
}
