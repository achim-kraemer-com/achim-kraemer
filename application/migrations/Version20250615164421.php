<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615164421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_signature ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE email_signature ADD CONSTRAINT FK_A1839E6A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_A1839E6A9395C3F3 ON email_signature (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_signature DROP FOREIGN KEY FK_A1839E6A9395C3F3');
        $this->addSql('DROP INDEX IDX_A1839E6A9395C3F3 ON email_signature');
        $this->addSql('ALTER TABLE email_signature DROP customer_id');
    }
}
