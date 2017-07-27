<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170729075233 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE places_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE travels_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pictures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE places (id INT NOT NULL, title VARCHAR(30) NOT NULL, latitude NUMERIC(18, 12) NOT NULL, longitude NUMERIC(18, 12) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEAF6C552B36786B ON places (title)');
        $this->addSql('CREATE INDEX places_title ON places (title)');
        $this->addSql('CREATE TABLE travels (id INT NOT NULL, place_id INT DEFAULT NULL, user_id INT DEFAULT NULL, start DATE NOT NULL, "end" DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67FF2BD7DA6A219 ON travels (place_id)');
        $this->addSql('CREATE INDEX IDX_67FF2BD7A76ED395 ON travels (user_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, locale VARCHAR(3) NOT NULL, roles TEXT NOT NULL, first_connection BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, registration_in_progress BOOLEAN NOT NULL, registration_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE INDEX users_username ON users (username)');
        $this->addSql('CREATE INDEX users_registration_code ON users (registration_code)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE pictures (id INT NOT NULL, travel_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0ECAB15B3 ON pictures (travel_id)');
        $this->addSql('ALTER TABLE travels ADD CONSTRAINT FK_67FF2BD7DA6A219 FOREIGN KEY (place_id) REFERENCES places (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE travels ADD CONSTRAINT FK_67FF2BD7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travels (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE travels DROP CONSTRAINT FK_67FF2BD7DA6A219');
        $this->addSql('ALTER TABLE pictures DROP CONSTRAINT FK_8F7C2FC0ECAB15B3');
        $this->addSql('ALTER TABLE travels DROP CONSTRAINT FK_67FF2BD7A76ED395');
        $this->addSql('DROP SEQUENCE places_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE travels_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pictures_id_seq CASCADE');
        $this->addSql('DROP TABLE places');
        $this->addSql('DROP TABLE travels');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE pictures');
    }
}
