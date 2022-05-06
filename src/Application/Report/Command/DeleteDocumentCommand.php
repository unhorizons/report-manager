<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Document;

/**
 * Class DeleteDocumentCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DeleteDocumentCommand
{
    public function __construct(
        public readonly Document $document,
    ) {
    }
}
