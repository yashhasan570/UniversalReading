<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Universal Reading\Doctrine\UniversalReadingMigration;

/**
 * Add the share_scuttle internal setting.
 */
class Version20170327194233 extends UniversalReadingMigration
{
    public function up(Schema $schema): void
    {
        $scuttle = $this->connection
            ->fetchOne('SELECT * FROM ' . $this->getTable('craue_config_setting') . " WHERE name = 'share_scuttle'");

        $this->skipIf(false !== $scuttle, 'It seems that you already played this migration.');

        $this->addSql('INSERT INTO ' . $this->getTable('craue_config_setting') . " (name, value, section) VALUES ('share_scuttle', '1', 'entry')");
        $this->addSql('INSERT INTO ' . $this->getTable('craue_config_setting') . " (name, value, section) VALUES ('scuttle_url', 'http://scuttle.org', 'entry')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM ' . $this->getTable('craue_config_setting') . " WHERE name = 'share_scuttle';");
        $this->addSql('DELETE FROM ' . $this->getTable('craue_config_setting') . " WHERE name = 'scuttle_url';");
    }
}
