<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105203822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, invoice_date DATE NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_906517449395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_time_entry (invoice_id INT NOT NULL, time_entry_id INT NOT NULL, INDEX IDX_978F0C922989F1FD (invoice_id), INDEX IDX_978F0C921EB30A8E (time_entry_id), PRIMARY KEY(invoice_id, time_entry_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_2FB3D0EE9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_entry (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, date DATE NOT NULL, hours NUMERIC(5, 2) NOT NULL, description LONGTEXT NOT NULL, invoiced TINYINT(1) NOT NULL, INDEX IDX_6E537C0C166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE invoice_time_entry ADD CONSTRAINT FK_978F0C922989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice_time_entry ADD CONSTRAINT FK_978F0C921EB30A8E FOREIGN KEY (time_entry_id) REFERENCES time_entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice_time_entry DROP FOREIGN KEY FK_978F0C922989F1FD');
        $this->addSql('ALTER TABLE invoice_time_entry DROP FOREIGN KEY FK_978F0C921EB30A8E');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE9395C3F3');
        $this->addSql('ALTER TABLE time_entry DROP FOREIGN KEY FK_6E537C0C166D1F9C');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_time_entry');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE time_entry');
    }
}
