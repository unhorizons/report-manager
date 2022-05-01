<?php

declare(strict_types=1);

namespace Domain\Report\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class IntervalDate.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class IntervalDate
{
    private readonly \DateTimeImmutable $starting_at;
    private readonly \DateTimeImmutable $ending_at;

    private function __construct(\DateTimeImmutable $starting_at, \DateTimeImmutable $ending_at)
    {
        $this->assertDateInCurrentYear($starting_at, 'report.validations.starting_not_current_year');
        $this->assertDateInCurrentYear($ending_at, 'report.validations.ending_not_current_year');
        $this->assertEndingGreaterThenStartingDate($starting_at, $ending_at);

        $this->starting_at = $starting_at;
        $this->ending_at = $ending_at;
    }

    public static function createDefault(): self
    {
        return new self(
            starting_at: new \DateTimeImmutable(),
            ending_at: new \DateTimeImmutable('+ 7 days')
        );
    }

    public static function fromArray(array $dates): self
    {
        Assert::notEmpty($dates, 'report.validations.empty_dates');

        return new self($dates[0], $dates[1]);
    }

    public function toArray(): array
    {
        return [$this->starting_at, $this->ending_at];
    }

    public function equals(self $date): bool
    {
        return $this->starting_at === $date->starting_at &&
            $this->ending_at === $date->ending_at;
    }

    public function getStartingAt(): \DateTimeImmutable
    {
        return $this->starting_at;
    }

    public function getEndingAt(): \DateTimeImmutable
    {
        return $this->ending_at;
    }

    private function assertDateInCurrentYear(\DateTimeImmutable $date, string $message): void
    {
        $currentYear = (new \DateTimeImmutable())->format('Y');
        if ($date->format('Y') !== $currentYear) {
            throw new \InvalidArgumentException($message);
        }
    }

    private function assertEndingGreaterThenStartingDate(
        \DateTimeImmutable $starting_at,
        \DateTimeImmutable $ending_at
    ): void {
        if ($ending_at < $starting_at) {
            throw new \InvalidArgumentException('report.validations.invalid_end_date');
        }
    }
}
