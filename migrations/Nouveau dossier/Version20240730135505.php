<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240730135505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stages DROP FOREIGN KEY FK_2FA26A64DDEAB1A3');
        $this->addSql('ALTER TABLE stages DROP FOREIGN KEY FK_2FA26A64A4AEAFEA');
        $this->addSql('DROP TABLE stages');
        $this->addSql('ALTER TABLE user ADD verification_code INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stages (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT DEFAULT NULL, entreprise_id INT DEFAULT NULL, domaine VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nombre INT NOT NULL, objet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_fin DATE NOT NULL, cv VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2FA26A64A4AEAFEA (entreprise_id), INDEX IDX_2FA26A64DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stages ADD CONSTRAINT FK_2FA26A64DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stages ADD CONSTRAINT FK_2FA26A64A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP verification_code');
    }
}
