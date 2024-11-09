<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031094122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE assignment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE assignment (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_scanable BOOLEAN NOT NULL, is_vehicle BOOLEAN NOT NULL, is_exercise BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP INDEX uniq_identifier_email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON "user" (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE assignment_id_seq CASCADE');
        $this->addSql('DROP TABLE assignment');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_LOGIN');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
    }
}
