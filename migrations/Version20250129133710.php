<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129133710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_group ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_group ADD CONSTRAINT FK_4189183A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_4189183A7E3C61F9 ON private_group (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_group DROP FOREIGN KEY FK_4189183A7E3C61F9');
        $this->addSql('DROP INDEX IDX_4189183A7E3C61F9 ON private_group');
        $this->addSql('ALTER TABLE private_group DROP owner_id');
    }
}
