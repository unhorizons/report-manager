<?php

declare(strict_types=1);

namespace Domain\Report\Repository;

use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface EvaluationRepositoryInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface EvaluationRepositoryInterface extends DataRepositoryInterface
{
    public function addEvaluationToReport(Evaluation $evaluation, Report $report): void;

    public function findAllEvaluationForReport(Report $report): array;

    public function findAllEvaluationForEmployee(User $employee): array;
}
