<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607093529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_has_type (movie_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_D7417FB8F93B6FC (movie_id), INDEX IDX_D7417FBC54C8C93 (type_id), PRIMARY KEY(movie_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_has_people (id INT AUTO_INCREMENT NOT NULL, Movie_id INT DEFAULT NULL, People_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, significance VARCHAR(255) NOT NULL,  INDEX IDX_EDC40D8176E5D4AA (Movie_id), INDEX IDX_EDC40D81B3B64B95 (People_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE people (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, nationality VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie_has_type ADD CONSTRAINT FK_D7417FB8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE movie_has_type ADD CONSTRAINT FK_D7417FBC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE movie_has_people ADD CONSTRAINT FK_EDC40D8176E5D4AA FOREIGN KEY (Movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE movie_has_people ADD CONSTRAINT FK_EDC40D81B3B64B95 FOREIGN KEY (People_id) REFERENCES people (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie_has_type DROP FOREIGN KEY FK_D7417FB8F93B6FC');
        $this->addSql('ALTER TABLE movie_has_type DROP FOREIGN KEY FK_D7417FBC54C8C93');
        $this->addSql('ALTER TABLE movie_has_people DROP FOREIGN KEY FK_EDC40D8176E5D4AA');
        $this->addSql('ALTER TABLE movie_has_people DROP FOREIGN KEY FK_EDC40D81B3B64B95');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE movie_has_type');
        $this->addSql('DROP TABLE movie_has_people');
        $this->addSql('DROP TABLE people');
        $this->addSql('DROP TABLE type');
    }
}
