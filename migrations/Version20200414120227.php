<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Universal Reading\Doctrine\UniversalReadingMigration;

/**
 * Remove baggy theme.
 */
final class Version20200414120227 extends UniversalReadingMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE ' . $this->getTable('config', true) . " SET theme = 'material';");
    }

    public function down(Schema $schema): void
    {
        throw new SkipMigration('Not possible ... ');
    }
}
