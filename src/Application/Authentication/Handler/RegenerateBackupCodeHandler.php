<?php

declare(strict_types=1);

namespace Application\Authentication\Handler;

use Application\Authentication\Command\RegenerateBackupCodeCommand;
use Application\Authentication\Service\CodeGeneratorService;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Class RegenerateBackupCodeHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsMessageHandler]
final class RegenerateBackupCodeHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly CodeGeneratorService $codeGeneratorService
    ) {
    }

    public function __invoke(RegenerateBackupCodeCommand $command): void
    {
        $user = $command->user;
        $codes = [];
        for ($i = 0; $i <= 5; ++$i) {
            $codes[$i] = $this->codeGeneratorService->generate();
        }

        $user->setBackupCode($codes);
        $this->repository->save($user);
    }
}
