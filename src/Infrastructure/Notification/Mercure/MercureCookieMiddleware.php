<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Mercure;

use Domain\Authentication\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

/**
 * Ajoute le cookie nécessaire à mercure sur les réponses.
 */
class MercureCookieMiddleware implements EventSubscriberInterface
{
    public function __construct(
        private readonly MercureCookieGenerator $generator,
        private readonly Security $security
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['setMercureCookie'],
        ];
    }

    public function setMercureCookie(ResponseEvent $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (
            HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType() ||
            !in_array('text/html', $request->getAcceptableContentTypes()) ||
            !$user instanceof User
        ) {
            return;
        }

        $response->headers->setCookie($this->generator->generate($user));
    }
}
