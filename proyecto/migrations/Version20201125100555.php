<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125100555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX name ON user');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP roles');
        $this->addSql('CREATE UNIQUE INDEX name ON user (name)');
    }
}
