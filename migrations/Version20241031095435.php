<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031095435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE assignment_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE assignment_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE squad_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assignment_type (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_scanable BOOLEAN NOT NULL, is_vehicle BOOLEAN NOT NULL, is_exercise BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE squad_member (id INT NOT NULL, member_id INT NOT NULL, scan_code VARCHAR(255) DEFAULT NULL, name1 VARCHAR(255) DEFAULT NULL, name2 VARCHAR(255) DEFAULT NULL, name3 VARCHAR(255) DEFAULT NULL, rank VARCHAR(255) DEFAULT NULL, last_assignment_timestamp TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE assignment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE assignment_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE squad_member_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE assignment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assignment (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_scanable BOOLEAN NOT NULL, is_vehicle BOOLEAN NOT NULL, is_exercise BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE assignment_type');
        $this->addSql('DROP TABLE squad_member');
    }
}
