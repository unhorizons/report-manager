<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Mailer;

use Domain\Authentication\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * class UserNotificationEmailTrait.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait UserNotificationEmailTrait
{
    private function createEmail(object $event, string $template, string $subject, string $domain = 'messages'): Email
    {
        if (!property_exists($event, 'user')) {
            throw new \RuntimeException('Event must have a reference to the user !');
        }

        /** @var User $user */
        $user = $event->user;

        return $this->mailer
            ->createEmail(
                template: $template,
                data: [
                    'user' => $user,
                    'event' => $event
                ]
            )->subject($this->translator->trans(
                id: $subject,
                parameters: [],
                domain: $domain
            ))
            ->to(new Address((string) $user->getEmail(), (string) $user->getUsername()));
    }
}
