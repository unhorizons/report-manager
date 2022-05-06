<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteEvaluationCommand;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteEvaluationHandler
{
    public function __construct(
        private readonly EvaluationRepositoryInterface $repository
    ) {
    }

    public function __invoke(DeleteEvaluationCommand $command): void
    {
        $this->repository->delete($command->evaluation);
    }
}
