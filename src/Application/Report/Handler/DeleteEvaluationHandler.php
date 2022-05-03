<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\DeleteEvaluationCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteEvaluationHandler
{
    public function __invoke(DeleteEvaluationCommand $command): void
    {
    }
}
