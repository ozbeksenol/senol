<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604183803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F52993989395C3F3 ON "order" (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE order_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity INTEGER NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52EA1F094584665A ON order_item (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, category INTEGER NOT NULL, stock INTEGER NOT NULL)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "order"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
    }
}
