<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Security\Voter;

use Domain\Report\Entity\Evaluation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class EvaluationVoter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationVoter extends Voter
{
    public const ATTRIBUTES = [
        'EVALUATION_DELETE',
        'EVALUATION_EDIT',
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($subject, self::ATTRIBUTES, true)) {
            return false;
        }

        if (! $subject instanceof Evaluation) {
            return false;
        }

        return true;
    }

    /**
     * @param Evaluation $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (null === $user) {
            return false;
        }

        return $subject->getManager() === $user;
    }
}
