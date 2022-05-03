<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class EvaluationVoter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return false;
    }
}
