<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\UpdateEvaluationCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateEvaluationHandler
{
    public function __invoke(UpdateEvaluationCommand $command): void
    {
    }
}
