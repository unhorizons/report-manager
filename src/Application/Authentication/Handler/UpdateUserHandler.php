<?php

declare(strict_types=1);

namespace Application\Authentication\Handler;

use Application\Authentication\Command\UpdateUserCommand;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $command->user;
        $user = $user
            ->setUsername($command->username)
            ->setEmail($command->email)
            ->setGender($command->gender)
            ->setJobTitle($command->job_title)
            ->disableEmailAuthCode()
            ->disableGoogleAuthenticator();
        $this->repository->save($user);
    }
}
