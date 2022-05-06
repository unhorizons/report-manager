<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteReportCommand;
use Domain\Report\Exception\NonMutableReportException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteReportHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository
    ) {
    }

    public function __invoke(DeleteReportCommand $command): void
    {
        $report = $command->report;
        if (! $report->isMutable()) {
            throw new NonMutableReportException();
        }

        $this->repository->delete($report);
    }
}
