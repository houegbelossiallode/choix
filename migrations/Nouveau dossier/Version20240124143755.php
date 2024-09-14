<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124143755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attestation (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, contrat_stage VARCHAR(255) NOT NULL, carte_scolaire VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_326EC63FA4AEAFEA (entreprise_id), INDEX IDX_326EC63FDDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attestation ADD CONSTRAINT FK_326EC63FA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attestation ADD CONSTRAINT FK_326EC63FDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stage CHANGE entreprise_id entreprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE image image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attestation DROP FOREIGN KEY FK_326EC63FA4AEAFEA');
        $this->addSql('ALTER TABLE attestation DROP FOREIGN KEY FK_326EC63FDDEAB1A3');
        $this->addSql('DROP TABLE attestation');
        $this->addSql('ALTER TABLE stage CHANGE entreprise_id entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE image image VARCHAR(255) DEFAULT NULL');
    }
}
