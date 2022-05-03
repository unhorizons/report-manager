<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20220503022419
 * @package DoctrineMigrations
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220503022419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'report and evaluation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, report_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1323A5754BD2A4C0 (report_id), INDEX IDX_1323A575783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, document_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', starting_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ending_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status ENUM(\'seen\', \'unseen\'), UNIQUE INDEX UNIQ_C42F7784D17F50A6 (uuid), INDEX IDX_C42F77848C03F15C (employee_id), INDEX IDX_C42F7784ADC98984A903D470 (starting_at, ending_at), INDEX IDX_C42F77847B00651C (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5754BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77848C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5754BD2A4C0');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE report');
    }
}
