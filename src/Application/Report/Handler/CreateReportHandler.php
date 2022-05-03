<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\CreateReportCommand;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\ReportRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateReportHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository
    ) {
    }

    public function __invoke(CreateReportCommand $command): void
    {
        $report = (new Report())
            ->setName($command->name)
            ->setDescription($command->description)
            ->setEmployee($command->employee)
            ->setIntervalDate((array) $command->interval_date)
        ;

        $this->repository->save($report);
    }
}
