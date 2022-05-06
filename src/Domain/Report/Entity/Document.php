<?php

declare(strict_types=1);

namespace Domain\Report\Entity;

use Domain\Shared\Entity\IdentityTrait;
use Domain\Shared\Entity\TimestampTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * Class Document.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Document
{
    use IdentityTrait;
    use TimestampTrait;

    private Uuid $uuid;

    private ?\SplFileInfo $file = null;

    private ?string $file_url = null;

    private ?int $file_size = null;

    private ?string $file_type = null;

    private ?Report $report = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    public function setFile(?\SplFileInfo $file): self
    {
        $this->file = $file;
        if ($this->file instanceof UploadedFile) {
            $this->updated_at = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->file_url;
    }

    public function setFileUrl(?string $file_url): self
    {
        $this->file_url = $file_url;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->file_size;
    }

    public function setFileSize(?int $file_size): self
    {
        $this->file_size = $file_size;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->file_type;
    }

    public function setFileType(?string $file_type): self
    {
        $this->file_type = $file_type;

        return $this;
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

    public function getReadableSize(): string
    {
        return round(($this->file_size / 1024) / 1024, 2) . ' Mb';
    }
}
