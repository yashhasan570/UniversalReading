<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Universal Reading\Doctrine\UniversalReadingMigration;

/**
 * Remove universalreading_url from craue_config_setting.
 * It has been moved into the parameters.yml.
 */
class Version20170606155640 extends UniversalReadingMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable($this->getTable('craue_config_setting'))) {
            $this->write('Table already renamed');

            return;
        }

        $apiUserRegistration = $this->connection
            ->fetchOne('SELECT * FROM ' . $this->getTable('craue_config_setting') . " WHERE name = 'universalreading_url'");

        if (false === $apiUserRegistration) {
            $this->write('It seems that you already played this migration.');

            return;
        }

        $this->addSql('DELETE FROM ' . $this->getTable('craue_config_setting') . " WHERE name = 'universalreading_url'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('INSERT INTO ' . $this->getTable('craue_config_setting') . " (name, value, section) VALUES ('universalreading_url', 'universalreading.me', 'misc')");
    }
}
