<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113175652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment_position ADD CONSTRAINT FK_822E9000D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assignment_position ADD CONSTRAINT FK_822E90009832F1DE FOREIGN KEY (squad_member_id) REFERENCES squad_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_822E9000D19302F8 ON assignment_position (assignment_id)');
        $this->addSql('CREATE INDEX IDX_822E90009832F1DE ON assignment_position (squad_member_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment_position DROP CONSTRAINT FK_822E9000D19302F8');
        $this->addSql('ALTER TABLE assignment_position DROP CONSTRAINT FK_822E90009832F1DE');
        $this->addSql('DROP INDEX IDX_822E9000D19302F8');
        $this->addSql('DROP INDEX IDX_822E90009832F1DE');
    }
}
