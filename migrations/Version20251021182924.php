<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021182924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, city, country, latitude, longitude FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(2) NOT NULL, latitude NUMERIC(10, 7) NOT NULL, longitude NUMERIC(10, 7) NOT NULL)');
        $this->addSql('INSERT INTO location (id, city, country, latitude, longitude) SELECT id, city, country, latitude, longitude FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
        $this->addSql('CREATE TEMPORARY TABLE __temp__weather AS SELECT id, date, celcius, wind_speed, precipitation, humidity FROM weather');
        $this->addSql('DROP TABLE weather');
        $this->addSql('CREATE TABLE weather (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_id INTEGER NOT NULL, date DATE NOT NULL, celcius NUMERIC(5, 1) NOT NULL, wind_speed DOUBLE PRECISION NOT NULL, precipitation DOUBLE PRECISION NOT NULL, humidity DOUBLE PRECISION NOT NULL, CONSTRAINT FK_4CD0D36E64D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO weather (id, date, celcius, wind_speed, precipitation, humidity) SELECT id, date, celcius, wind_speed, precipitation, humidity FROM __temp__weather');
        $this->addSql('DROP TABLE __temp__weather');
        $this->addSql('CREATE INDEX IDX_4CD0D36E64D218E ON weather (location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, city, country, latitude, longitude FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, weather_id INTEGER NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(2) NOT NULL, latitude NUMERIC(10, 7) NOT NULL, longitude NUMERIC(10, 7) NOT NULL, CONSTRAINT FK_5E9E89CB8CE675E FOREIGN KEY (weather_id) REFERENCES weather (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO location (id, city, country, latitude, longitude) SELECT id, city, country, latitude, longitude FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
        $this->addSql('CREATE INDEX IDX_5E9E89CB8CE675E ON location (weather_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__weather AS SELECT id, date, celcius, wind_speed, precipitation, humidity FROM weather');
        $this->addSql('DROP TABLE weather');
        $this->addSql('CREATE TABLE weather (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATE NOT NULL, celcius NUMERIC(3, 0) NOT NULL, wind_speed DOUBLE PRECISION NOT NULL, precipitation DOUBLE PRECISION NOT NULL, humidity DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO weather (id, date, celcius, wind_speed, precipitation, humidity) SELECT id, date, celcius, wind_speed, precipitation, humidity FROM __temp__weather');
        $this->addSql('DROP TABLE __temp__weather');
    }
}
