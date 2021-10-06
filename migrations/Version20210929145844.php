<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Entity\User;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929145844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users CHANGE role_id role_id INT DEFAULT ' . User::DEFAULT_ROLE_ID . ' NOT NULL, CHANGE status_id status_id INT DEFAULT ' . User::DEFAULT_STATUS_ID . ' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users CHANGE role_id role_id INT NOT NULL, CHANGE status_id status_id INT NOT NULL');
    }
}
