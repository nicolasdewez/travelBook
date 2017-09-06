<?php

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170902155140 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE places ADD locale VARCHAR(3) NOT NULL');
        $this->addSql('DROP INDEX places_title');
        $this->addSql('DROP INDEX UNIQ_FEAF6C552B36786B');
        $this->addSql('CREATE UNIQUE INDEX places_unique ON places (title, locale)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX places_unique');
        $this->addSql('CREATE INDEX UNIQ_FEAF6C552B36786B ON places (title)');
        $this->addSql('CREATE INDEX places_title ON places (title)');
        $this->addSql('ALTER TABLE places DROP locale');
    }
}
