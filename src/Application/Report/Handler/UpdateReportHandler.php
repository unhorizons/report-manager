<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\UpdateReportCommand;
use Domain\Report\Exception\NonMutableReportException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateReportHandler
{
    public function __invoke(UpdateReportCommand $command): void
    {
        $report = $command->report;
        if ($report->isMutable()) {
            throw new NonMutableReportException();
        }
    }
}
