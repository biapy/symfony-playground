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
 * Add comment and user tables.
 */
final class Version20250430073414 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'Add comment and user tables';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE comment (id UUID NOT NULL, blog_post_id UUID NOT NULL, author_id UUID NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CA77FBEAF ON comment (blog_post_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN comment.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN comment.blog_post_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN comment.author_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id UUID NOT NULL, display_name VARCHAR(255) DEFAULT NULL, email VARCHAR(320) NOT NULL, password_hash VARCHAR(128) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".email IS '(DC2Type:Nepada\EmailAddress\RfcEmailAddress)'
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO "user" (id, display_name, email, password_hash, created_at, updated_at) VALUES (
                'ecf98ac0-02d6-54df-8ac6-c7c8894c3953', 'None', 'none@localhost', '', NOW(), NOW()
            )
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blog_post ADD author_id UUID NOT NULL DEFAULT 'ecf98ac0-02d6-54df-8ac6-c7c8894c3953'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN blog_post.author_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BA5AE01DF675F31B ON blog_post (author_id)
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01DF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP CONSTRAINT FK_9474526CA77FBEAF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BA5AE01DF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blog_post DROP author_id
        SQL);
    }
}
