<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250427121641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE SEQUENCE episode_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE episode (id INT NOT NULL, name VARCHAR(255) NOT NULL, air_date DATE NOT NULL, episode_code VARCHAR(10) NOT NULL, characters JSON NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");

        $this->addSql("CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE review (id INT NOT NULL, episode_id INT NOT NULL, content TEXT NOT NULL, sentiment_score DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_794381C6362B62A0 ON review (episode_id)");
        $this->addSql("ALTER TABLE review ADD CONSTRAINT FK_794381C6362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP SEQUENCE review_id_seq CASCADE");
        $this->addSql("ALTER TABLE review DROP CONSTRAINT FK_794381C6362B62A0");
        $this->addSql("DROP TABLE review");

        $this->addSql("DROP SEQUENCE episode_id_seq CASCADE");
        $this->addSql("DROP TABLE episode");
    }
}
