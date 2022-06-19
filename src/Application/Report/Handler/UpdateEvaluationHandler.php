<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\UpdateEvaluationCommand;
use Domain\Report\Event\EvaluationUpdatedEvent;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateEvaluationHandler
{
    public function __construct(
        private readonly EvaluationRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(UpdateEvaluationCommand $command): void
    {
        $evaluation = $command->evaluation;
        $evaluation->setContent($command->content);
        $this->repository->save($evaluation);
        $this->dispatcher->dispatch(new EvaluationUpdatedEvent($evaluation));
    }
}
