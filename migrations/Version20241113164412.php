<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113164412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment_category ADD root_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment_category ADD CONSTRAINT FK_C24E6D3435E19FEF FOREIGN KEY (root_category_id) REFERENCES assignment_root_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C24E6D3435E19FEF ON assignment_category (root_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment_category DROP CONSTRAINT FK_C24E6D3435E19FEF');
        $this->addSql('DROP INDEX IDX_C24E6D3435E19FEF');
        $this->addSql('ALTER TABLE assignment_category DROP root_category_id');
    }
}
