<?php

declare(strict_types=1);

namespace Domain\Report\Exception;

use Domain\Shared\Exception\SafeMessageException;

/**
 * Class ReportForPeriodAlreadyExistsException.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportForPeriodAlreadyExistsException extends SafeMessageException
{
    protected string $messageDomain = 'report';

    public function __construct(
        string $message = 'report.exceptions.report_for_period_already_exists',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
