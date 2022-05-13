<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\Period;
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

    private Period $period;

    private ?User $employee = null;

    /**
     * @var Collection<User>
     */
    private Collection $managers;

    /**
     * @var Collection<Evaluation>
     */
    private Collection $evaluations;

    /**
     * @var Collection<Document>
     */
    private Collection $documents;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->status = Status::unseen();
        $this->period = Period::createForPreviousWeek();
        $this->documents = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->managers = new ArrayCollection();
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

    public function getPeriod(): Period
    {
        return $this->period;
    }

    public function setPeriod(Period $period): self
    {
        $this->period = $period;

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

    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (! $this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setReport($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            if ($evaluation->getReport() === $this) {
                $evaluation->setReport(null);
            }
        }

        return $this;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): self
    {
        if (null !== $documents) {
            foreach ($documents as $document) {
                $d = (new Document())->setReport($this)->setFile($document);
                $this->addDocument($d);
            }

            $this->updated_at = new \DateTimeImmutable();
        }

        return $this;
    }

    public function addDocument(Document|\SplFileInfo $document): self
    {
        if ($document instanceof Document) {
            if (! $this->documents->contains($document)) {
                $this->documents[] = $document;
                $document->setReport($this);
            }
        } else {
            (new Document())->setReport($this)->setFile($document);
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

    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(User $manager): self
    {
        if (! $this->managers->contains($manager)) {
            $this->managers[] = $manager;
        }

        return $this;
    }

    public function removeManager(User $manager): self
    {
        $this->managers->removeElement($manager);

        return $this;
    }
}
