<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210528084202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, auteur_article_id INT NOT NULL, theme_id INT DEFAULT NULL, pays_id INT DEFAULT NULL, texte LONGTEXT NOT NULL, date_creation DATETIME DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_23A0E6693F4A80 (auteur_article_id), INDEX IDX_23A0E6659027487 (theme_id), INDEX IDX_23A0E66A6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galerie (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, img VARCHAR(255) NOT NULL, legende VARCHAR(255) DEFAULT NULL, INDEX IDX_9E7D15907294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, projets_voyages LONGTEXT DEFAULT NULL, voyages_accomplis LONGTEXT DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_naissance DATE NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6693F4A80 FOREIGN KEY (auteur_article_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6659027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE galerie ADD CONSTRAINT FK_9E7D15907294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE galerie DROP FOREIGN KEY FK_9E7D15907294869C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A6E44244');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6659027487');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6693F4A80');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE galerie');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
    }
}
