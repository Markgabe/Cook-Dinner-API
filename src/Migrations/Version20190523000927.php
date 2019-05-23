<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190523000927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_691B4722B91BA9FB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, favorito, nota FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, favorito BOOLEAN NOT NULL, nota INTEGER DEFAULT NULL, CONSTRAINT FK_691B4722B91BA9FB FOREIGN KEY (receita_id) REFERENCES receita (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, favorito, nota) SELECT id, receita_id, favorito, nota FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__receita AS SELECT id, nome, descricao, image FROM receita');
        $this->addSql('DROP TABLE receita');
        $this->addSql('CREATE TABLE receita (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nome VARCHAR(255) NOT NULL COLLATE BINARY, descricao CLOB DEFAULT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_5A2897AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO receita (id, nome, descricao, image) SELECT id, nome, descricao, image FROM __temp__receita');
        $this->addSql('DROP TABLE __temp__receita');
        $this->addSql('CREATE INDEX IDX_5A2897AAA76ED395 ON receita (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_691B4722B91BA9FB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, nota, favorito FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, nota INTEGER DEFAULT NULL, favorito BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, nota, favorito) SELECT id, receita_id, nota, favorito FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
        $this->addSql('DROP INDEX IDX_5A2897AAA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__receita AS SELECT id, nome, descricao, image FROM receita');
        $this->addSql('DROP TABLE receita');
        $this->addSql('CREATE TABLE receita (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, descricao CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO receita (id, nome, descricao, image) SELECT id, nome, descricao, image FROM __temp__receita');
        $this->addSql('DROP TABLE __temp__receita');
    }
}
