<?php

declare(strict_types=1);

namespace Application\Authentication\Handler;

use Application\Authentication\Command\DeleteUserCommand;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $this->repository->delete($command->user);
    }
}
