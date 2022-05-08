<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Security\Voter;

use Domain\Report\Entity\Document;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class DocumentVoter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DocumentVoter extends Voter
{
    public const ATTRIBUTES = [
        'DOCUMENT_DELETE',
        'DOCUMENT_DOWNLOAD',
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($subject, self::ATTRIBUTES, true)) {
            return false;
        }

        if (! $subject instanceof Document) {
            return false;
        }

        return true;
    }

    /**
     * @param Document $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (null === $user) {
            return false;
        }

        return $subject->getReport()?->getEmployee() === $user;
    }
}
