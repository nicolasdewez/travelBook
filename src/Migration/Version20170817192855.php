<?php

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170817192855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pictures ADD check_state VARCHAR(15) NOT NULL, ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE travels ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pictures ADD place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0DA6A29 FOREIGN KEY (place_id) REFERENCES places (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0DA6A219 ON pictures (place_id)');
        $this->addSql('CREATE SEQUENCE invalidation_pictures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE invalidation_pictures (id INT NOT NULL, picture_id INT DEFAULT NULL, reason VARCHAR(15) NOT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C24D2C5EE45BDBF ON invalidation_pictures (picture_id)');
        $this->addSql('ALTER TABLE invalidation_pictures ADD CONSTRAINT FK_1C24D2C5EE45BDBF FOREIGN KEY (picture_id) REFERENCES pictures (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pictures DROP CONSTRAINT FK_8F7C2FC0DA6A29');
        $this->addSql('ALTER TABLE pictures DROP place_id, DROP check_state, DROP name');
        $this->addSql('ALTER TABLE travels DROP title');
        $this->addSql('DROP SEQUENCE invalidation_pictures_id_seq CASCADE');
        $this->addSql('ALTER TABLE invalidation_pictures DROP CONSTRAINT FK_1C24D2C5EE45BDBF');
        $this->addSql('DROP TABLE invalidation_pictures');
    }
}
