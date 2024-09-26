<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926165619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, hotel_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, disponible TINYINT(1) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_C509E4FF3243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chambre_picture (chambre_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_B5CD1C759B177F54 (chambre_id), INDEX IDX_B5CD1C75EE45BDBF (picture_id), PRIMARY KEY(chambre_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, gerant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(550) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_3535ED9A500A924 (gerant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(850) DEFAULT NULL, is_principale TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_chambre (reservation_id INT NOT NULL, chambre_id INT NOT NULL, INDEX IDX_A29C5F7AB83297E7 (reservation_id), INDEX IDX_A29C5F7A9B177F54 (chambre_id), PRIMARY KEY(reservation_id, chambre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE chambre_picture ADD CONSTRAINT FK_B5CD1C759B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chambre_picture ADD CONSTRAINT FK_B5CD1C75EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED9A500A924 FOREIGN KEY (gerant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation_chambre ADD CONSTRAINT FK_A29C5F7AB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_chambre ADD CONSTRAINT FK_A29C5F7A9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF3243BB18');
        $this->addSql('ALTER TABLE chambre_picture DROP FOREIGN KEY FK_B5CD1C759B177F54');
        $this->addSql('ALTER TABLE chambre_picture DROP FOREIGN KEY FK_B5CD1C75EE45BDBF');
        $this->addSql('ALTER TABLE hotel DROP FOREIGN KEY FK_3535ED9A500A924');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation_chambre DROP FOREIGN KEY FK_A29C5F7AB83297E7');
        $this->addSql('ALTER TABLE reservation_chambre DROP FOREIGN KEY FK_A29C5F7A9B177F54');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE chambre_picture');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_chambre');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
