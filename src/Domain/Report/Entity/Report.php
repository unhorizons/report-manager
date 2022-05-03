<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\IntervalDate;
use Domain\Report\ValueObject\Status;
use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    private ?File $document_file = null;

    private ?string $document_url = null;

    private ?User $employee = null;

    /**
     * @var Evaluation[]
     */
    private array $evaluations = [];

    public function __construct()
    {
        $this->uuid = Uuid::v4();
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

    public function getDocumentFile(): ?File
    {
        return $this->document_file;
    }

    public function setDocumentFile(?File $document_file): self
    {
        $this->document_file = $document_file;
        if ($this->document_file instanceof UploadedFile) {
            $this->updated_at = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getDocumentUrl(): ?string
    {
        return $this->document_url;
    }

    public function setDocumentUrl(?string $document_url): self
    {
        $this->document_url = $document_url;

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
}
