<?php

declare(strict_types=1);

namespace Domain\Report\Exception;

use Domain\Shared\Exception\SafeMessageException;

final class EmptySearchQueryException extends SafeMessageException
{
    protected string $messageDomain = 'report';

    public function __construct(
        string $message = 'report.exceptions.empty_search_query',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
