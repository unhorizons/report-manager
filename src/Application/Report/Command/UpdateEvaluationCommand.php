<?php

declare(strict_types=1);

namespace Application\Report\Command;

/**
 * Class UpdateEvaluationCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateEvaluationCommand
{
    public function __construct(
        public ?string $content = null
    ) {
    }
}
