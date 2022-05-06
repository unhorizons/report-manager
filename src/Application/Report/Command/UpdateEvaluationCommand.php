<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Evaluation;

/**
 * Class UpdateEvaluationCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateEvaluationCommand
{
    public function __construct(
        public readonly Evaluation $evaluation,
        public ?string $content = null
    ) {
        $this->content = $this->evaluation->getContent();
    }
}
