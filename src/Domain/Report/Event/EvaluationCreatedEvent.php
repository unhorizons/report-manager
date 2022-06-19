<?php

declare(strict_types=1);

namespace Domain\Report\Event;

use Domain\Report\Entity\Evaluation;

/**
 * Class EvaluationCreatedEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationCreatedEvent
{
    public function __construct(
        public readonly Evaluation $evaluation
    ) {
    }
}
