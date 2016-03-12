<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Mozcu\MozcuBundle\Entity\User;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160312215632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD status INT NOT NULL');
        $this->addSql('UPDATE user SET status = ' . User::STATUS_INACTIVE . ' WHERE is_active = 0');
        $this->addSql('UPDATE user SET status = ' . User::STATUS_ACTIVE . ' WHERE is_active = 1');
        $this->addSql('ALTER TABLE user DROP is_active');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD is_active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE user SET is_active = 0 WHERE status = ' . User::STATUS_INACTIVE);
        $this->addSql('UPDATE user SET is_active = 1 WHERE status = ' . User::STATUS_ACTIVE);
        $this->addSql('ALTER TABLE user DROP status');
    }
}
