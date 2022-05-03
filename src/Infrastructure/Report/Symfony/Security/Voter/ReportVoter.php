<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Security\Voter;

use Domain\Report\Entity\Report;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class ReportVoter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportVoter extends Voter
{
    public const ATTRIBUTES = [
        'REPORT_VIEW',
        'REPORT_DELETE',
        'REPORT_EDIT',
        'REPORT_UPDATE',
    ];

    public function __construct(
        private readonly Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($subject, self::ATTRIBUTES, true)) {
            return false;
        }

        if (! $subject instanceof Report) {
            return false;
        }

        return true;
    }

    /**
     * @param Report $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (null === $user) {
            return false;
        }

        if (
            $this->security->isGranted('ROLE_ADMIN') ||
            $this->security->isGranted('ROLE_REPORT_MANAGER') ||
            $subject->getEmployee() === $user
        ) {
            return true;
        }

        return false;
    }
}
