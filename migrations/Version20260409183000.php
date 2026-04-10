<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409183000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_description DROP NOT NULL');
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_status DROP NOT NULL');
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_archivedTask DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_description SET NOT NULL');
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_status SET NOT NULL');
        $this->addSql('ALTER TABLE task_group ALTER taskgroup_archivedtask SET NOT NULL');
    }
}
