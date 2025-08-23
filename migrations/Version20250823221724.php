<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Enable ltree extension, create MyEntity table, and messenger_messages table.
 */
final class Version20250823221724 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'Enable ltree extension, create MyEntity table, and messenger_messages table';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE EXTENSION IF NOT EXISTS ltree');
        $this->addSql('CREATE TABLE my_entity (id UUID NOT NULL, parent_id UUID DEFAULT NULL, path ltree NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_924D8473B548B0F ON my_entity (path)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_924D84735E237E06 ON my_entity (name)');
        $this->addSql('CREATE INDEX IDX_924D8473727ACA70 ON my_entity (parent_id)');
        $this->addSql('CREATE INDEX my_entity_path_gist_idx ON my_entity USING GIST (path gist_ltree_ops(siglen = 100))');
        $this->addSql("COMMENT ON COLUMN my_entity.id IS '(DC2Type:uuid)'");
        $this->addSql("COMMENT ON COLUMN my_entity.parent_id IS '(DC2Type:uuid)'");
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql("COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE my_entity ADD CONSTRAINT FK_924D8473727ACA70 FOREIGN KEY (parent_id) REFERENCES my_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE my_entity DROP CONSTRAINT FK_924D8473727ACA70');
        $this->addSql('DROP TABLE my_entity');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
