<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031124957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment_group ADD is_scanable BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD is_vehicle BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD is_exercise BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP scanable');
        $this->addSql('ALTER TABLE assignment_group DROP vehicle');
        $this->addSql('ALTER TABLE assignment_group DROP exercise');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment_group ADD scanable BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD vehicle BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD exercise BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP is_scanable');
        $this->addSql('ALTER TABLE assignment_group DROP is_vehicle');
        $this->addSql('ALTER TABLE assignment_group DROP is_exercise');
    }
}
