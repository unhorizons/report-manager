<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\SearchReportCommand;
use Domain\Report\Exception\EmptySearchQueryException;
use Domain\Report\Exception\NoSearchResultException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SearchReportHandler
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository
    ) {
    }

    public function __invoke(SearchReportCommand $command): array
    {
        $user = $command->user;
        $options = [
            'period' => $command->period,
            'seen' => $command->seen,
            'unseen' => $command->unseen,
            'use_period' => $command->use_period,
        ];

        if ($user->hasRole('ROLE_REPORT_MANAGER')) {
            $data = $this->repository->searchForManager($user, $command->query, $options);
        } else {
            $data = $this->repository->search($command->query, $options);
        }

        if (0 === count($data)) {
            throw new NoSearchResultException();
        }

        return $data;
    }
}
