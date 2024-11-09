<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241109092433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE vehicle_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE assignment_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assignment_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, category_group INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE assignment_category_assignment_group (assignment_category_id INT NOT NULL, assignment_group_id INT NOT NULL, PRIMARY KEY(assignment_category_id, assignment_group_id))');
        $this->addSql('CREATE INDEX IDX_433E14F3414F1C3 ON assignment_category_assignment_group (assignment_category_id)');
        $this->addSql('CREATE INDEX IDX_433E14F33A56ADB2 ON assignment_category_assignment_group (assignment_group_id)');
        $this->addSql('ALTER TABLE assignment_category_assignment_group ADD CONSTRAINT FK_433E14F3414F1C3 FOREIGN KEY (assignment_category_id) REFERENCES assignment_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assignment_category_assignment_group ADD CONSTRAINT FK_433E14F33A56ADB2 FOREIGN KEY (assignment_group_id) REFERENCES assignment_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('ALTER TABLE assignment_group DROP CONSTRAINT fk_94229d67727aca70');
        $this->addSql('DROP INDEX idx_94229d67727aca70');
        $this->addSql('ALTER TABLE assignment_group ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD member_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP parent_id');
        $this->addSql('ALTER TABLE assignment_group DROP scanable');
        $this->addSql('ALTER TABLE assignment_group DROP vehicles_enabled');
        $this->addSql('ALTER TABLE assignment_group DROP exercise_enabled');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE assignment_category_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE vehicle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vehicle (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, passenger_count INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE assignment_category_assignment_group DROP CONSTRAINT FK_433E14F3414F1C3');
        $this->addSql('ALTER TABLE assignment_category_assignment_group DROP CONSTRAINT FK_433E14F33A56ADB2');
        $this->addSql('DROP TABLE assignment_category');
        $this->addSql('DROP TABLE assignment_category_assignment_group');
        $this->addSql('ALTER TABLE assignment_group ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD scanable BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD vehicles_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD exercise_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE assignment_group DROP description');
        $this->addSql('ALTER TABLE assignment_group DROP member_count');
        $this->addSql('ALTER TABLE assignment_group ADD CONSTRAINT fk_94229d67727aca70 FOREIGN KEY (parent_id) REFERENCES assignment_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_94229d67727aca70 ON assignment_group (parent_id)');
    }
}
