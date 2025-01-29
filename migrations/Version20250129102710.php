<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129102710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE private_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_group_user (private_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A90C274E59C206EA (private_group_id), INDEX IDX_A90C274EA76ED395 (user_id), PRIMARY KEY(private_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE private_group_user ADD CONSTRAINT FK_A90C274E59C206EA FOREIGN KEY (private_group_id) REFERENCES private_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_group_user ADD CONSTRAINT FK_A90C274EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_user DROP FOREIGN KEY FK_2CCED92A76ED395');
        $this->addSql('ALTER TABLE outing_user DROP FOREIGN KEY FK_2CCED92AF4C7531');
        $this->addSql('DROP TABLE outing_user');
        $this->addSql('ALTER TABLE outing ADD private_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A1062559C206EA FOREIGN KEY (private_group_id) REFERENCES private_group (id)');
        $this->addSql('CREATE INDEX IDX_F2A1062559C206EA ON outing (private_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A1062559C206EA');
        $this->addSql('CREATE TABLE outing_user (outing_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2CCED92AF4C7531 (outing_id), INDEX IDX_2CCED92A76ED395 (user_id), PRIMARY KEY(outing_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92AF4C7531 FOREIGN KEY (outing_id) REFERENCES outing (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_group_user DROP FOREIGN KEY FK_A90C274E59C206EA');
        $this->addSql('ALTER TABLE private_group_user DROP FOREIGN KEY FK_A90C274EA76ED395');
        $this->addSql('DROP TABLE private_group');
        $this->addSql('DROP TABLE private_group_user');
        $this->addSql('DROP INDEX IDX_F2A1062559C206EA ON outing');
        $this->addSql('ALTER TABLE outing DROP private_group_id');
    }
}
