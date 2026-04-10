<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409163204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD taskgroup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2595FD12D0 FOREIGN KEY (taskgroup_id) REFERENCES task_group (taskgroup_id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_527EDB2595FD12D0 ON task (taskgroup_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2595FD12D0');
        $this->addSql('DROP INDEX IDX_527EDB2595FD12D0');
        $this->addSql('ALTER TABLE task DROP taskgroup_id');
    }
}
