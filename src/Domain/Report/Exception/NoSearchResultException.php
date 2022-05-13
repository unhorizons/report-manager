<?php

declare(strict_types=1);

namespace Domain\Report\Exception;

use Domain\Shared\Exception\SafeMessageException;

/**
 * Class NoSearchResultException.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NoSearchResultException extends SafeMessageException
{
    protected string $messageDomain = 'report';

    public function __construct(
        string $message = 'report.exceptions.no_search_result',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
