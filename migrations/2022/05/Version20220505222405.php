<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20220505222405
 * @package DoctrineMigrations
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220505222405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'document table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, report_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', file_url VARCHAR(255) NOT NULL, file_type VARCHAR(15) NOT NULL, file_size INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_D8698A76D17F50A6 (uuid), INDEX IDX_D8698A764BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('DROP INDEX IDX_C42F7784ADC98984A903D470 ON report');
        $this->addSql('ALTER TABLE report ADD period_source VARCHAR(255) NOT NULL, ADD period_starting_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD period_ending_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP starting_at, DROP ending_at, CHANGE status status ENUM(\'seen\', \'unseen\'), CHANGE document_url period_hash VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX IDX_C42F7784D91052737DDDAFA26263422C ON report (period_starting_at, period_ending_at, period_source)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP INDEX IDX_C42F7784D91052737DDDAFA26263422C ON report');
        $this->addSql('ALTER TABLE report ADD document_url VARCHAR(255) NOT NULL, ADD starting_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD ending_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP period_hash, DROP period_source, DROP period_starting_at, DROP period_ending_at, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_C42F7784ADC98984A903D470 ON report (starting_at, ending_at)');
    }
}
