<?php

declare(strict_types=1);

namespace Domain\Report\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class IntervalDate.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Period implements \Stringable
{
    private readonly \DateTimeImmutable $starting_at;
    private readonly \DateTimeImmutable $ending_at;
    private readonly string $hash;
    private readonly string $source;

    private function __construct(\DateTimeImmutable $starting_at, \DateTimeImmutable $ending_at)
    {
        $this->assertDateInCurrentYear($ending_at);
        $this->assertEndingGreaterThenStartingDate($starting_at, $ending_at);
        //$this->assertWeekPeriod($starting_at, $ending_at);
        //$this->assertStartOnMondayEndOnSaturday($starting_at, $ending_at);

        $this->starting_at = $starting_at;
        $this->ending_at = $ending_at;

        $this->source = $starting_at->format('Ymd') . '-' . $ending_at->format('Ymd');
        $this->hash = md5($this->source);
    }

    public function __toString(): string
    {
        return $this->starting_at->format('d M Y') . ' - ' . $this->ending_at->format('d M Y');
    }

    public static function createForPreviousWeek(): self
    {
        return new self(
            starting_at: new \DateTimeImmutable('- 6 days'),
            ending_at: new \DateTimeImmutable()
        );
    }

    public static function fromArray(array $dates): self
    {
        Assert::notEmpty($dates, 'report.validations.empty_dates');

        $starting = \DateTimeImmutable::createFromInterface($dates[0]);
        $ending = \DateTimeImmutable::createFromInterface($dates[1]);

        return new self($starting, $ending);
    }

    public function toArray(): array
    {
        return [$this->starting_at, $this->ending_at];
    }

    public function equals(self $date): bool
    {
        return $date->hash === $this->hash;
    }

    public function getStartingAt(): \DateTimeImmutable
    {
        return $this->starting_at;
    }

    public function getEndingAt(): \DateTimeImmutable
    {
        return $this->ending_at;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    private function assertDateInCurrentYear(\DateTimeImmutable $date): void
    {
        $currentYear = (new \DateTimeImmutable())->format('Y');
        if ($date->format('Y') !== $currentYear) {
            throw new \InvalidArgumentException('report.validations.ending_not_current_year');
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

    private function assertWeekPeriod(\DateTimeImmutable $starting_at, \DateTimeImmutable $ending_at): void
    {
        $days = $ending_at->diff($starting_at)->days;
        if (6 === $days) {
            throw new \InvalidArgumentException('report.validations.weeK_interval');
        }
    }

    private function assertStartOnMondayEndOnSaturday(\DateTimeImmutable $starting_at, \DateTimeImmutable $ending_at): void
    {
        if (1 !== intval($starting_at->format('w')) || 6 !== intval($ending_at->format('w'))) {
            throw new \InvalidArgumentException('report.validations.week_days');
        }
    }
}
