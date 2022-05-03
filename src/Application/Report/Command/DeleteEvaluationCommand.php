<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Evaluation;

/**
 * Class DeleteEvaluationCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DeleteEvaluationCommand
{
    public function __construct(
        public readonly Evaluation $evaluation
    ) {
    }
}
