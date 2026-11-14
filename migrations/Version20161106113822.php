<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Universal Reading\Doctrine\UniversalReadingMigration;

/**
 * Added action_mark_as_read field on config table.
 */
class Version20161106113822 extends UniversalReadingMigration
{
    public function up(Schema $schema): void
    {
        $configTable = $schema->getTable($this->getTable('config'));

        $this->skipIf($configTable->hasColumn('action_mark_as_read'), 'It seems that you already played this migration.');

        $configTable->addColumn('action_mark_as_read', 'integer', [
            'default' => 0,
            'notnull' => false,
        ]);
    }

    public function down(Schema $schema): void
    {
        $configTable = $schema->getTable($this->getTable('config'));

        $this->skipIf(!$configTable->hasColumn('action_mark_as_read'), 'It seems that you already played this migration.');

        $configTable->dropColumn('action_mark_as_read');
    }
}
