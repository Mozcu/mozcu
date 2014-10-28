<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141027222651 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C61137ABCF');
        $this->addSql('ALTER TABLE album_upload_queue_pending DROP INDEX UNIQ_C50778A21137ABCF, ADD INDEX IDX_C50778A21137ABCF (album_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE album_upload_queue_pending DROP INDEX IDX_C50778A21137ABCF, ADD UNIQUE INDEX UNIQ_C50778A21137ABCF (album_id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C61137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
    }
}
