<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031135302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment_group ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assignment_group ADD CONSTRAINT FK_94229D67727ACA70 FOREIGN KEY (parent_id) REFERENCES assignment_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_94229D67727ACA70 ON assignment_group (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment_group DROP CONSTRAINT FK_94229D67727ACA70');
        $this->addSql('DROP INDEX IDX_94229D67727ACA70');
        $this->addSql('ALTER TABLE assignment_group DROP parent_id');
    }
}
