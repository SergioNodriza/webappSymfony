<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125105853 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (
                                id INT AUTO_INCREMENT NOT NULL, 
                                user_id INT NOT NULL, 
                                title VARCHAR(255) NOT NULL, 
                                done TINYINT(1) DEFAULT NULL, 
                                created_at DATETIME NOT NULL, 
                                INDEX IDX_1F1B251EA76ED395 (user_id), PRIMARY KEY(id)
                                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE user (
                                id INT AUTO_INCREMENT NOT NULL, 
                                name VARCHAR(255) NOT NULL, 
                                password VARCHAR(255) NOT NULL, PRIMARY KEY(id),
                                roles JSON NOT NULL,
                                UNIQUE (name)
                                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT unique_title_user UNIQUE (title, user_id);');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EA76ED395');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE user');
    }
}
