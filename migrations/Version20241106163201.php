<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106163201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE vehicle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vehicle (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, passenger_count INT DEFAULT 4 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE assignment_group ADD vehicles_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD standby_position_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD exercise_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP vehicle');
        $this->addSql('ALTER TABLE assignment_group DROP exercise');
        $this->addSql('ALTER TABLE assignment_group ALTER scanable SET DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE vehicle_id_seq CASCADE');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('ALTER TABLE assignment_group ADD vehicle BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD exercise BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP vehicles_enabled');
        $this->addSql('ALTER TABLE assignment_group DROP standby_position_enabled');
        $this->addSql('ALTER TABLE assignment_group DROP exercise_enabled');
        $this->addSql('ALTER TABLE assignment_group ALTER scanable DROP DEFAULT');
    }
}
