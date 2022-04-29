<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\EventSubscriber;

use Application\Authentication\Command\RegisterLoginIpAddressCommand;
use Domain\Authentication\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LoginEventSubscriber.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class LoginEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            ResetPasswordConfirmedEvent::class => 'onResetPasswordConfirmed',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $ip = (string) $event->getRequest()->getClientIp();
        $this->commandBus->dispatch(new RegisterLoginIpAddressCommand($user, $ip));
    }

    public function onResetPasswordConfirmed(ResetPasswordConfirmedEvent $event): void
    {
        // TODO: send mail
    }
}
