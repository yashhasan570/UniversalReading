<?php

namespace Application\Migrations;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Universal Reading\Doctrine\UniversalReadingMigration;

/**
 * Changed length for username, username_canonical, email and email_canonical fields in universalreading_user table.
 */
class Version20170510082609 extends UniversalReadingMigration
{
    private $fields = [
        'username',
        'username_canonical',
        'email',
        'email_canonical',
    ];

    public function up(Schema $schema): void
    {
        if (!$this->connection->getDatabasePlatform() instanceof MySQLPlatform) {
            $this->write('This migration only apply to MySQL');

            return;
        }

        foreach ($this->fields as $field) {
            $this->addSql('ALTER TABLE ' . $this->getTable('user') . ' CHANGE ' . $field . ' ' . $field . ' VARCHAR(180) NOT NULL;');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$this->connection->getDatabasePlatform() instanceof MySQLPlatform) {
            $this->write('This migration only apply to MySQL');

            return;
        }

        foreach ($this->fields as $field) {
            $this->addSql('ALTER TABLE ' . $this->getTable('user') . ' CHANGE ' . $field . ' ' . $field . ' VARCHAR(255) NOT NULL;');
        }
    }
}
