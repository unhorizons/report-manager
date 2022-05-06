<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\CreateReportCommand;
use Domain\Report\Entity\Report;
use Domain\Report\Exception\ReportForPeriodAlreadyExistsException;
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
        if ($this->repository->hasReportMatchingHashForEmployee($command->employee, $command->period->getHash())) {
            throw new ReportForPeriodAlreadyExistsException();
        }

        $report = (new Report())
            ->setName($command->name)
            ->setDescription($command->description)
            ->setEmployee($command->employee)
            ->setDocuments($command->documents)
            ->setPeriod($command->period);

        $this->repository->save($report);
    }
}
