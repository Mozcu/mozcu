<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150415200006 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX search_idx ON tag (name)');
        $this->addSql('CREATE INDEX livesearch_idx ON profile (name)');
        $this->addSql('CREATE INDEX livesearch_idx ON album (name, artist_name)');
        $this->addSql('CREATE INDEX livesearch_idx ON song (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX livesearch_idx ON album');
        $this->addSql('DROP INDEX livesearch_idx ON profile');
        $this->addSql('DROP INDEX livesearch_idx ON song');
        $this->addSql('DROP INDEX search_idx ON tag');
    }
}
