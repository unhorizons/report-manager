<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteReportCommand;
use Domain\Report\Exception\NonMutableReportException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteReportHandler
{
    public function __invoke(DeleteReportCommand $command): void
    {
        $report = $command->report;
        if ($report->isMutable()) {
            throw new NonMutableReportException();
        }
    }
}
