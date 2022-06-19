<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\SetReportSeenStatusCommand;
use Domain\Report\Event\ReportSeenEvent;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Status;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SetReportSeenStatusHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(SetReportSeenStatusCommand $command): void
    {
        $report = $command->report;
        if ($report->getStatus()->equals(Status::unseen())) {
            $report->setStatus(Status::seen());
            $this->repository->save($report);
            $this->dispatcher->dispatch(new ReportSeenEvent($report));
        }
    }
}
