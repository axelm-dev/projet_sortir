<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223143602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP INDEX UNIQ_F515E139876C4DDA, ADD INDEX IDX_F515E139876C4DDA (organizer_id)');
        $this->addSql('ALTER TABLE meeting ADD annulation_reason VARCHAR(255) DEFAULT NULL, DROP nb_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP INDEX IDX_F515E139876C4DDA, ADD UNIQUE INDEX UNIQ_F515E139876C4DDA (organizer_id)');
        $this->addSql('ALTER TABLE meeting ADD nb_user INT DEFAULT NULL, DROP annulation_reason');
    }
}
