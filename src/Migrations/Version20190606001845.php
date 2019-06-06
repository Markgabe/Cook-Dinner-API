<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190606001845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_6BAF787059D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredient AS SELECT id, recipe_id, name, amount FROM ingredient');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('CREATE TABLE ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, amount VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_6BAF787059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ingredient (id, recipe_id, name, amount) SELECT id, recipe_id, name, amount FROM __temp__ingredient');
        $this->addSql('DROP TABLE __temp__ingredient');
        $this->addSql('CREATE INDEX IDX_6BAF787059D8A214 ON ingredient (recipe_id)');
        $this->addSql('DROP INDEX IDX_DFEC3F3959D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__rate AS SELECT id, recipe_id, grade, favorite, created_at FROM rate');
        $this->addSql('DROP TABLE rate');
        $this->addSql('CREATE TABLE rate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, grade INTEGER DEFAULT NULL, favorite BOOLEAN NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_DFEC3F3959D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO rate (id, recipe_id, grade, favorite, created_at) SELECT id, recipe_id, grade, favorite, created_at FROM __temp__rate');
        $this->addSql('DROP TABLE __temp__rate');
        $this->addSql('CREATE INDEX IDX_DFEC3F3959D8A214 ON rate (recipe_id)');
        $this->addSql('DROP INDEX IDX_DA88B137A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, user_id, name, description, image, time, created_at, portion, grade FROM recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, time INTEGER NOT NULL, created_at DATETIME NOT NULL, portion VARCHAR(255) DEFAULT NULL COLLATE BINARY, grade DOUBLE PRECISION DEFAULT NULL, CONSTRAINT FK_DA88B137A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recipe (id, user_id, name, description, image, time, created_at, portion, grade) SELECT id, user_id, name, description, image, time, created_at, portion, grade FROM __temp__recipe');
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('CREATE INDEX IDX_DA88B137A76ED395 ON recipe (user_id)');
        $this->addSql('DROP INDEX IDX_43B9FE3C59D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, recipe_id, number, description FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, number INTEGER NOT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_43B9FE3C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO step (id, recipe_id, number, description) SELECT id, recipe_id, number, description FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3C59D8A214 ON step (recipe_id)');
        $this->addSql('ALTER TABLE user ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_F7129A80233D34C1');
        $this->addSql('DROP INDEX IDX_F7129A803AD8644E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_user AS SELECT user_source, user_target FROM user_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('CREATE TABLE user_user (user_source INTEGER NOT NULL, user_target INTEGER NOT NULL, PRIMARY KEY(user_source, user_target), CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_user (user_source, user_target) SELECT user_source, user_target FROM __temp__user_user');
        $this->addSql('DROP TABLE __temp__user_user');
        $this->addSql('CREATE INDEX IDX_F7129A80233D34C1 ON user_user (user_target)');
        $this->addSql('CREATE INDEX IDX_F7129A803AD8644E ON user_user (user_source)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_6BAF787059D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredient AS SELECT id, recipe_id, name, amount FROM ingredient');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('CREATE TABLE ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, amount VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO ingredient (id, recipe_id, name, amount) SELECT id, recipe_id, name, amount FROM __temp__ingredient');
        $this->addSql('DROP TABLE __temp__ingredient');
        $this->addSql('CREATE INDEX IDX_6BAF787059D8A214 ON ingredient (recipe_id)');
        $this->addSql('DROP INDEX IDX_DFEC3F3959D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__rate AS SELECT id, recipe_id, grade, favorite, created_at FROM rate');
        $this->addSql('DROP TABLE rate');
        $this->addSql('CREATE TABLE rate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, grade INTEGER DEFAULT NULL, favorite BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO rate (id, recipe_id, grade, favorite, created_at) SELECT id, recipe_id, grade, favorite, created_at FROM __temp__rate');
        $this->addSql('DROP TABLE __temp__rate');
        $this->addSql('CREATE INDEX IDX_DFEC3F3959D8A214 ON rate (recipe_id)');
        $this->addSql('DROP INDEX IDX_DA88B137A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, user_id, name, description, image, time, created_at, portion, grade FROM recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, time INTEGER NOT NULL, created_at DATETIME NOT NULL, portion VARCHAR(255) DEFAULT NULL, grade DOUBLE PRECISION DEFAULT NULL)');
        $this->addSql('INSERT INTO recipe (id, user_id, name, description, image, time, created_at, portion, grade) SELECT id, user_id, name, description, image, time, created_at, portion, grade FROM __temp__recipe');
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('CREATE INDEX IDX_DA88B137A76ED395 ON recipe (user_id)');
        $this->addSql('DROP INDEX IDX_43B9FE3C59D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, recipe_id, number, description FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, number INTEGER NOT NULL, description VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO step (id, recipe_id, number, description) SELECT id, recipe_id, number, description FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3C59D8A214 ON step (recipe_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, birthday, gender, created_at, email, password, roles FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, birthday DATETIME DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO "user" (id, name, birthday, gender, created_at, email, password, roles) SELECT id, name, birthday, gender, created_at, email, password, roles FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('DROP INDEX IDX_F7129A803AD8644E');
        $this->addSql('DROP INDEX IDX_F7129A80233D34C1');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_user AS SELECT user_source, user_target FROM user_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('CREATE TABLE user_user (user_source INTEGER NOT NULL, user_target INTEGER NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('INSERT INTO user_user (user_source, user_target) SELECT user_source, user_target FROM __temp__user_user');
        $this->addSql('DROP TABLE __temp__user_user');
        $this->addSql('CREATE INDEX IDX_F7129A803AD8644E ON user_user (user_source)');
        $this->addSql('CREATE INDEX IDX_F7129A80233D34C1 ON user_user (user_target)');
    }
}
