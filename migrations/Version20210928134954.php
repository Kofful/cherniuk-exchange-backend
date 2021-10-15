<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210928134954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inventory_items (id INT AUTO_INCREMENT NOT NULL, sticker_id INT NOT NULL, owner_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3D82424D7E3C61F9 (owner_id), INDEX IDX_3D82424D4D965A4D (sticker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_items (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, sticker_id INT NOT NULL, is_accept TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_623394A453C674EE (offer_id), INDEX IDX_623394A44D965A4D (sticker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offers (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, target_id INT DEFAULT NULL, status_id INT NOT NULL, creator_payment INT NOT NULL, target_payment INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DA46042761220EA6 (creator_id), INDEX IDX_DA460427158E0B66 (target_id), INDEX IDX_DA4604276BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stickers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, coefficient INT NOT NULL, chance INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, status_id INT NOT NULL, username VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(256) NOT NULL, wallet INT DEFAULT 0 NOT NULL, rewarded_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1483A5E9D60322AC (role_id), INDEX IDX_1483A5E96BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory_items ADD CONSTRAINT FK_3D82424D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE inventory_items ADD CONSTRAINT FK_3D82424D4D965A4D FOREIGN KEY (sticker_id) REFERENCES stickers (id)');
        $this->addSql('ALTER TABLE offer_items ADD CONSTRAINT FK_623394A453C674EE FOREIGN KEY (offer_id) REFERENCES offers (id)');
        $this->addSql('ALTER TABLE offer_items ADD CONSTRAINT FK_623394A44D965A4D FOREIGN KEY (sticker_id) REFERENCES stickers (id)');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA46042761220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA460427158E0B66 FOREIGN KEY (target_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA4604276BF700BD FOREIGN KEY (status_id) REFERENCES offer_statuses (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E96BF700BD FOREIGN KEY (status_id) REFERENCES user_statuses (id)');

        $this->addSql('INSERT INTO roles (name) VALUES ("ROLE_USER"), ("ROLE_ADMIN")');
        $this->addSql('INSERT INTO offer_statuses (name) VALUES ("open"), ("pending"), ("closed")');
        $this->addSql('INSERT INTO user_statuses (name) VALUES ("unconfirmed"), ("confirmed"), ("banned")');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA4604276BF700BD');
        $this->addSql('ALTER TABLE offer_items DROP FOREIGN KEY FK_623394A453C674EE');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('ALTER TABLE inventory_items DROP FOREIGN KEY FK_3D82424D4D965A4D');
        $this->addSql('ALTER TABLE offer_items DROP FOREIGN KEY FK_623394A44D965A4D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E96BF700BD');
        $this->addSql('ALTER TABLE inventory_items DROP FOREIGN KEY FK_3D82424D7E3C61F9');
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA46042761220EA6');
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA460427158E0B66');
        $this->addSql('DROP TABLE inventory_items');
        $this->addSql('DROP TABLE offer_items');
        $this->addSql('DROP TABLE offer_statuses');
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE stickers');
        $this->addSql('DROP TABLE user_statuses');
        $this->addSql('DROP TABLE users');
    }
}
