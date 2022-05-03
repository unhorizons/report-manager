<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Domain\Authentication\Entity\User;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;

/**
 * Class Evaluation.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Evaluation
{
    use IdentityTrait;
    use TimestampTrait;

    private ?Report $report = null;

    private ?User $manager = null;

    private ?string $content = null;

    public static function createForReport(string $content, User $manager, Report $report): self
    {
        return (new self())
            ->setContent($content)
            ->setManager($manager)
            ->setReport($report);
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
