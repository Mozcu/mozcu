<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Mozcu\MozcuBundle\Entity\Album;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160312183759 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE album ADD status INT NOT NULL');
        $this->addSql('UPDATE album SET status = ' . Album::STATUS_DELETED . ' WHERE is_active = 0');
        $this->addSql('UPDATE album SET status = ' . Album::STATUS_ACTIVE . ' WHERE is_active = 1');
        $this->addSql('ALTER TABLE album DROP is_active');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE album ADD is_active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE album SET is_active = 0 WHERE status = ' . Album::STATUS_DELETED);
        $this->addSql('UPDATE album SET is_active = 1 WHERE status = ' . Album::STATUS_ACTIVE);
        $this->addSql('ALTER TABLE album DROP status');
    }
}
