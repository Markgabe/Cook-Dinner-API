<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522221834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('DROP INDEX IDX_691B4722B91BA9FB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, favorito, nota FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, favorito BOOLEAN NOT NULL, nota INTEGER DEFAULT NULL, CONSTRAINT FK_691B4722B91BA9FB FOREIGN KEY (receita_id) REFERENCES receita (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, favorito, nota) SELECT id, receita_id, favorito, nota FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_691B4722B91BA9FB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, nota, favorito FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, nota INTEGER DEFAULT NULL, favorito BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, nota, favorito) SELECT id, receita_id, nota, favorito FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
    }
}
