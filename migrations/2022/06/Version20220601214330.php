<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20220518013758
 * @package DoctrineMigrations
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20220601214330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2fa support';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD is_email_auth_enabled TINYINT(1) DEFAULT 0 NOT NULL, ADD email_auth_code VARCHAR(6) DEFAULT NULL, ADD is_google_authenticator_enabled TINYINT(1) DEFAULT 0 NOT NULL, ADD google_authenticator_secret VARCHAR(255) DEFAULT NULL, ADD backup_codes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP is_email_auth_enabled, DROP email_auth_code, DROP is_google_authenticator_enabled, DROP google_authenticator_secret, DROP backup_codes');
    }
}
