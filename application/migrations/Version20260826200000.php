<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds the DC2Type marker to time_entry.timer_started_at.
 *
 * Version20260719120000 created the column as a plain DATETIME, while the
 * entity maps it as datetime_immutable. Doctrine stores that distinction in a
 * column comment, so without it every schema diff keeps regenerating the same
 * ALTER. Purely a metadata fix; the stored values are unaffected.
 */
final class Version20260826200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mark time_entry.timer_started_at as datetime_immutable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE time_entry CHANGE timer_started_at timer_started_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry CHANGE timer_started_at timer_started_at DATETIME DEFAULT NULL');
    }
}
