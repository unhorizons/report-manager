<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteEvaluationCommand;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\ReportRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteEvaluationHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository
    ) {
    }

    public function __invoke(DeleteEvaluationCommand $command): void
    {
        /** @var Report $report */
        $report = $command->evaluation->getReport();
        $report->removeEvaluation($command->evaluation);

        $this->repository->save($report);
    }
}
