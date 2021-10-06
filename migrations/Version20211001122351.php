<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211001122351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD confirmation_code VARCHAR(64) DEFAULT NULL, CHANGE role_id role_id INT NOT NULL, CHANGE status_id status_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP confirmation_code, CHANGE role_id role_id INT DEFAULT 1 NOT NULL, CHANGE status_id status_id INT DEFAULT 1 NOT NULL');
    }
}
