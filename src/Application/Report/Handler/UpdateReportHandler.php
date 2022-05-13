<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\UpdateReportCommand;
use Domain\Authentication\Entity\User;
use Domain\Report\Exception\NonMutableReportException;
use Domain\Report\Exception\ReportForPeriodAlreadyExistsException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateReportHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository
    ) {
    }

    public function __invoke(UpdateReportCommand $command): void
    {
        $report = $command->report;
        /** @var User $employee */
        $employee = $report->getEmployee();

        if (! $report->isMutable()) {
            throw new NonMutableReportException();
        }

        if (
            $this->repository->findMatchingHashForEmployee(
                employee: $employee,
                hash: $command->period->getHash(),
                excludeReportUuid: $report->getUuid()->toBinary()
            )
        ) {
            throw new ReportForPeriodAlreadyExistsException();
        }

        $report = $report
            ->setName($command->name)
            ->setDescription($command->description)
            ->setDocuments($command->documents)
            ->setPeriod($command->period);

        $this->repository->save($report);
    }
}
