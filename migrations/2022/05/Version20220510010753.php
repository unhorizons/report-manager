<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20220510010753
 * @package DoctrineMigrations
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220510010753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'report assignable to chosen managers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE manager_assigned_report (report_id INT NOT NULL, manager_id INT NOT NULL, INDEX IDX_1E37D64C4BD2A4C0 (report_id), INDEX IDX_1E37D64C783E3463 (manager_id), PRIMARY KEY(report_id, manager_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manager_assigned_report ADD CONSTRAINT FK_1E37D64C4BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('ALTER TABLE manager_assigned_report ADD CONSTRAINT FK_1E37D64C783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE manager_assigned_report');
    }
}
