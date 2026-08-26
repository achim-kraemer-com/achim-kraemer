<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add timer columns (start timestamp + accumulated seconds) to time_entry.
 */
final class Version20260719120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add timer_started_at and accumulated_seconds to time_entry';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry ADD timer_started_at DATETIME DEFAULT NULL, ADD accumulated_seconds INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry DROP timer_started_at, DROP accumulated_seconds');
    }
}
