<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\IntervalDate;
use Domain\Report\ValueObject\Status;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class Report.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Report
{
    use IdentityTrait;
    use TimestampTrait;

    private ?string $name = null;

    private ?string $description = null;

    private Status $status;

    private IntervalDate $interval_date;

    private ?File $document_file;

    private ?string $document_url;

    private ?User $user = null;

    public function __construct()
    {
        $this->status = Status::unseen();
        $this->interval_date = IntervalDate::createDefault();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIntervalDate(): IntervalDate
    {
        return $this->interval_date;
    }

    public function setIntervalDate(IntervalDate $date): self
    {
        $this->interval_date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status|string $status): self
    {
        if ($status instanceof Status) {
            $this->status = $status;
        } else {
            $this->status = Status::fromString($status);
        }

        return $this;
    }
}
