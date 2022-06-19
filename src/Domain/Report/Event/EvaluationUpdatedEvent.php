<?php

declare(strict_types=1);

namespace Domain\Report\Event;

use Domain\Report\Entity\Evaluation;

/**
 * Class EvaluationUpdatedEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationUpdatedEvent
{
    public function __construct(
        public readonly Evaluation $evaluation
    ) {
    }
}
