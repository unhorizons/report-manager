<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\IntervalDate;
use Domain\Report\ValueObject\Status;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;
use Symfony\Component\Uid\Uuid;

/**
 * Class Report.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Report
{
    use IdentityTrait;
    use TimestampTrait;

    private Uuid $uuid;

    private ?string $name = null;

    private ?string $description = null;

    private Status $status;

    private IntervalDate $interval_date;

    private ?User $employee = null;

    /**
     * @var Evaluation[]
     */
    private array $evaluations = [];

    /**
     * @var Collection<Document>
     */
    private Collection $documents;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->status = Status::unseen();
        $this->interval_date = IntervalDate::createDefault();
        $this->documents = new ArrayCollection();
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

    public function setIntervalDate(IntervalDate|array $date): self
    {
        if ($date instanceof IntervalDate) {
            $this->interval_date = $date;
        } else {
            $this->interval_date = IntervalDate::fromArray($date);
        }

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

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

    public function isMutable(): bool
    {
        return $this->status->equals(Status::unseen());
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid|string $uuid): self
    {
        if ($uuid instanceof Uuid) {
            $this->uuid = $uuid;
        } else {
            $this->uuid = Uuid::fromString($uuid);
        }

        return $this;
    }

    public function getEvaluations(): array
    {
        return $this->evaluations;
    }

    public function setEvaluations(array $evaluations): self
    {
        $this->evaluations = $evaluations;

        return $this;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (! $this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setReport($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getReport() === $this) {
                $document->setReport(null);
            }
        }

        return $this;
    }
}
