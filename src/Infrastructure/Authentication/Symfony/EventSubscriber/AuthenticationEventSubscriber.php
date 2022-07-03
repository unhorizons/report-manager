<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\EventSubscriber;

use Application\Authentication\Command\RegisterLoginIpAddressCommand;
use Domain\Authentication\Entity\User;
use Domain\Authentication\Event\DefaultPasswordCreatedEvent;
use Domain\Authentication\Event\LoginWithAnotherIpAddressEvent;
use Domain\Authentication\Event\PasswordUpdatedEvent;
use Domain\Authentication\Event\ResetPasswordConfirmedEvent;
use Domain\Authentication\Event\TwoFactorAuthDisabledEvent;
use Domain\Authentication\Event\TwoFactorAuthEnabledEvent;
use Infrastructure\Shared\Symfony\Mailer\Mailer;
use Infrastructure\Shared\Symfony\Mailer\UserNotificationEmailTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AuthenticationEventSubscriber.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AuthenticationEventSubscriber implements EventSubscriberInterface
{
    use UserNotificationEmailTrait;

    public function __construct(
        private readonly Mailer $mailer,
        private readonly TranslatorInterface $translator,
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            ResetPasswordConfirmedEvent::class => 'onResetPasswordConfirmed',
            PasswordUpdatedEvent::class => 'onPasswordUpdated',
            DefaultPasswordCreatedEvent::class => 'onDefaultPasswordCreated',
            TwoFactorAuthEnabledEvent::class => 'onTwoFactorAuthEnabled',
            TwoFactorAuthDisabledEvent::class => 'onTwoFactorAuthDisabled',
            LoginWithAnotherIpAddressEvent::class => 'onLoginWithAnotherIpAddressEvent',
        ];
    }

    public function onDefaultPasswordCreated(DefaultPasswordCreatedEvent $event): void
    {
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/default_password_created.mail.twig',
            subject: 'authentication.mails.subjects.default_password_created',
            domain: 'authentication'
        ));
    }

    public function onLoginWithAnotherIpAddressEvent(LoginWithAnotherIpAddressEvent $event): void
    {
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/login_with_another_ip_address.mail.twig',
            subject: 'authentication.mails.subjects.login_with_another_ip_address',
            domain: 'authentication'
        ));
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
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/reset_password_confirmed.mail.twig',
            subject: 'authentication.mails.subjects.reset_password_confirmed',
            domain: 'authentication'
        ));
    }

    public function onPasswordUpdated(PasswordUpdatedEvent $event): void
    {
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/password_updated.mail.twig',
            subject: 'authentication.mails.subjects.password_updated',
            domain: 'authentication'
        ));
    }

    public function onTwoFactorAuthEnabled(TwoFactorAuthEnabledEvent $event): void
    {
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/2fa_enabled.mail.twig',
            subject: 'authentication.mails.subjects.2fa_enabled',
            domain: 'authentication'
        ));
    }

    public function onTwoFactorAuthDisabled(TwoFactorAuthDisabledEvent $event): void
    {
        $this->mailer->send($this->createEmail(
            event: $event,
            template: 'domain/authentication/mail/2fa_disabled.mail.twig',
            subject: 'authentication.mails.subjects.2fa_disabled',
            domain: 'authentication'
        ));
    }
}
