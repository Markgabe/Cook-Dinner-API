<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522212948 extends AbstractMigration
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, nota, favorito FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, favorito BOOLEAN NOT NULL, nota INTEGER DEFAULT NULL, CONSTRAINT FK_691B4722B91BA9FB FOREIGN KEY (receita_id) REFERENCES receita (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, nota, favorito) SELECT id, receita_id, nota, favorito FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_691B4722B91BA9FB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avaliacao AS SELECT id, receita_id, nota, favorito FROM avaliacao');
        $this->addSql('DROP TABLE avaliacao');
        $this->addSql('CREATE TABLE avaliacao (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, receita_id INTEGER NOT NULL, favorito BOOLEAN NOT NULL, nota DOUBLE PRECISION DEFAULT NULL)');
        $this->addSql('INSERT INTO avaliacao (id, receita_id, nota, favorito) SELECT id, receita_id, nota, favorito FROM __temp__avaliacao');
        $this->addSql('DROP TABLE __temp__avaliacao');
        $this->addSql('CREATE INDEX IDX_691B4722B91BA9FB ON avaliacao (receita_id)');
    }
}
