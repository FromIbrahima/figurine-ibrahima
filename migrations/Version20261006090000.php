<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Création des tables "users" et "figurines".
 */
final class Version20261006090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables users et figurines (une figurine appartient à un seul utilisateur).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, image_name VARCHAR(500) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE figurines (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, image_name VARCHAR(500) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_45D9EB617E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE figurines ADD CONSTRAINT FK_45D9EB617E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE figurines DROP FOREIGN KEY FK_45D9EB617E3C61F9');
        $this->addSql('DROP TABLE figurines');
        $this->addSql('DROP TABLE users');
    }
}
