<?php

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170910194525 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE travels ADD start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE travels ADD end_date DATE NOT NULL');
        $this->addSql('ALTER TABLE travels DROP start');
        $this->addSql('ALTER TABLE travels DROP "end"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE travels ADD start DATE NOT NULL');
        $this->addSql('ALTER TABLE travels ADD "end" DATE NOT NULL');
        $this->addSql('ALTER TABLE travels DROP start_date');
        $this->addSql('ALTER TABLE travels DROP end_date');
    }
}
