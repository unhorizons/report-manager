<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;

/**
 * Class CreateEvaluationCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateEvaluationCommand
{
    public function __construct(
        public readonly User $manager,
        public readonly Report $report,
        public ?string $content = null,
    ) {
    }
}
