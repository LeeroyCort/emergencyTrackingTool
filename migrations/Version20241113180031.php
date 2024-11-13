<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113180031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment_position ADD assignment_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE assignment_position DROP scanned_type');
        $this->addSql('ALTER TABLE assignment_position ADD CONSTRAINT FK_822E90003A56ADB2 FOREIGN KEY (assignment_group_id) REFERENCES assignment_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_822E90003A56ADB2 ON assignment_position (assignment_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment_position DROP CONSTRAINT FK_822E90003A56ADB2');
        $this->addSql('DROP INDEX IDX_822E90003A56ADB2');
        $this->addSql('ALTER TABLE assignment_position ADD scanned_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment_position DROP assignment_group_id');
    }
}
