<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228135545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_items DROP FOREIGN KEY FK_623394A453C674EE');
        $this->addSql('ALTER TABLE offer_items ADD CONSTRAINT FK_623394A453C674EE FOREIGN KEY (offer_id) REFERENCES offers (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_items DROP FOREIGN KEY FK_623394A453C674EE');
        $this->addSql('ALTER TABLE offer_items ADD CONSTRAINT FK_623394A453C674EE FOREIGN KEY (offer_id) REFERENCES offers (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
