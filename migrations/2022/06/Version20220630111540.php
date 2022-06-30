<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * class Version20220630111540.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220630111540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Notification subscription';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification_push_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, endpoint VARCHAR(500) NOT NULL, expiration_time VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', keys_p256dh VARCHAR(255) NOT NULL, keys_auth VARCHAR(255) NOT NULL, INDEX IDX_95AEBA12A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_push_subscription ADD CONSTRAINT FK_95AEBA12A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification_push_subscription');
    }
}
