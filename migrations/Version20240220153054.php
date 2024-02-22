<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220153054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state_meeting (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meeting ADD state_id INT NOT NULL, ADD place_id INT NOT NULL, ADD campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E1395D83CC1 FOREIGN KEY (state_id) REFERENCES state_meeting (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_F515E1395D83CC1 ON meeting (state_id)');
        $this->addSql('CREATE INDEX IDX_F515E139DA6A219 ON meeting (place_id)');
        $this->addSql('CREATE INDEX IDX_F515E139AF5D55E1 ON meeting (campus_id)');
        $this->addSql('ALTER TABLE user ADD campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AF5D55E1 ON user (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139AF5D55E1');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E1395D83CC1');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE state_meeting');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139DA6A219');
        $this->addSql('DROP INDEX IDX_F515E1395D83CC1 ON meeting');
        $this->addSql('DROP INDEX IDX_F515E139DA6A219 ON meeting');
        $this->addSql('DROP INDEX IDX_F515E139AF5D55E1 ON meeting');
        $this->addSql('ALTER TABLE meeting DROP state_id, DROP place_id, DROP campus_id');
        $this->addSql('DROP INDEX IDX_8D93D649AF5D55E1 ON user');
        $this->addSql('ALTER TABLE user DROP campus_id');
    }
}
