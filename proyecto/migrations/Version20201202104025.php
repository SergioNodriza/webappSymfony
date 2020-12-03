<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202104025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Activate user';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE item ADD COLUMN activate TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE item DROP COLUMN activate');

    }
}
