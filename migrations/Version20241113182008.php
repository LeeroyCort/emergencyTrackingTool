<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113182008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment ADD root_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE assignment ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment DROP assignment_category_name');
        $this->addSql('ALTER TABLE assignment DROP assignment_root_category_id');
        $this->addSql('ALTER TABLE assignment DROP assignment_root_category_name');
        $this->addSql('ALTER TABLE assignment ALTER assignment_category_id SET NOT NULL');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA414F1C3 FOREIGN KEY (assignment_category_id) REFERENCES assignment_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA35E19FEF FOREIGN KEY (root_category_id) REFERENCES assignment_root_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_30C544BA414F1C3 ON assignment (assignment_category_id)');
        $this->addSql('CREATE INDEX IDX_30C544BA35E19FEF ON assignment (root_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment DROP CONSTRAINT FK_30C544BA414F1C3');
        $this->addSql('ALTER TABLE assignment DROP CONSTRAINT FK_30C544BA35E19FEF');
        $this->addSql('DROP INDEX IDX_30C544BA414F1C3');
        $this->addSql('DROP INDEX IDX_30C544BA35E19FEF');
        $this->addSql('ALTER TABLE assignment ADD assignment_root_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment ADD assignment_root_category_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment DROP root_category_id');
        $this->addSql('ALTER TABLE assignment ALTER assignment_category_id DROP NOT NULL');
        $this->addSql('ALTER TABLE assignment RENAME COLUMN name TO assignment_category_name');
    }
}
