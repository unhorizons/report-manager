<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * class Version20220630160420.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220630160420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'sigle notification read';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD is_read TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP is_read');
    }
}
