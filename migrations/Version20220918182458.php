<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918182458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE degat (id INT AUTO_INCREMENT NOT NULL, emprunt_id INT DEFAULT NULL, description VARCHAR(300) NOT NULL, cout_estimer INT NOT NULL, date_degat VARCHAR(40) NOT NULL, INDEX IDX_C07E13C7AE7FEF94 (emprunt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE degat ADD CONSTRAINT FK_C07E13C7AE7FEF94 FOREIGN KEY (emprunt_id) REFERENCES emprunt (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE degat DROP FOREIGN KEY FK_C07E13C7AE7FEF94');
        $this->addSql('DROP TABLE degat');
    }
}
