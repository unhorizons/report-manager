<?php

declare(strict_types=1);

namespace Application\Authentication\Handler;

use Application\Authentication\Command\RegisterUserCommand;
use Application\Authentication\Service\CodeGeneratorService;
use Domain\Authentication\Entity\User;
use Domain\Authentication\Event\DefaultPasswordCreatedEvent;
use Domain\Authentication\Exception\EmailAlreadyUsedException;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $user = $this->repository->findOneByEmail($command->email);
        if ($user) {
            throw new EmailAlreadyUsedException();
        }

        $password = $this->codeGeneratorService->generate(8);
        $user = (new User())
            ->setUsername($command->username)
            ->setEmail($command->email)
            ->setGender($command->gender)
            ->setJobTitle($command->job_title)
            ->setRoles($command->roles)
            ->disableEmailAuthCode()
            ->disableGoogleAuthenticator();
        $user->setPassword($this->hasher->hashPassword($user, (string) $password));

        $this->repository->save($user);
        $this->dispatcher->dispatch(new DefaultPasswordCreatedEvent($user, $password));
    }
}
