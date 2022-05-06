<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteDocumentCommand;
use Domain\Report\Exception\EmptyDocumentReportException;
use Domain\Report\Exception\NonMutableReportException;
use Domain\Report\Repository\DocumentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteDocumentHandler
{
    public function __construct(
        private readonly DocumentRepositoryInterface $repository,
    ) {
    }

    public function __invoke(DeleteDocumentCommand $command): void
    {
        $report = $command->document->getReport();
        if (! $report?->isMutable()) {
            throw new NonMutableReportException();
        }

        if (1 === $report->getDocuments()->count()) {
            throw new EmptyDocumentReportException();
        }

        $report->removeDocument($command->document);
        $this->repository->delete($command->document);
    }
}
