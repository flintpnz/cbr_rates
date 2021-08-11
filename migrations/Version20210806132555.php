<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210806132555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, id_currency VARCHAR(7) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rate (id INT AUTO_INCREMENT NOT NULL, id_currency_id INT NOT NULL, nominal INT NOT NULL, date DATE NOT NULL, value NUMERIC(10, 4) NOT NULL, INDEX IDX_E9521FAB20364C81 (id_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB20364C81 FOREIGN KEY (id_currency_id) REFERENCES currency (id)');
        $this->addSql('INSERT INTO `currency` (`id`, `id_currency`, `name`) VALUES (1, "R01010", "Австралийский доллар"), (2, "R01015", "Австрийский шиллинг"), (3, "R01020A", "Азербайджанский манат"), (4, "R01035", "Фунт стерлингов Соединенного королевства"), (5, "R01040F", "Ангольская новая кванза"), (6, "R01060", "Армянский драм"), (7, "R01090B", "Белорусский рубль"), (8, "R01095", "Бельгийский франк"), (9, "R01100", "Болгарский лев"), (10, "R01115", "Бразильский реал"), (11, "R01135", "Венгерский форинт"), (12, "R01200", "Гонконгский доллар"), (13, "R01205", "Греческая драхма"), (14, "R01215", "Датская крона"), (15, "R01235", "Доллар США"), (16, "R01239", "Евро"), (17, "R01270", "Индийская рупия"), (18, "R01305", "Ирландский фунт"), (19, "R01310", "Исландская крона"), (20, "R01315", "Испанская песета"), (21, "R01325", "Итальянская лира"), (22, "R01335", "Казахстанский тенге"), (23, "R01350", "Канадский доллар"), (24, "R01370", "Киргизский сом"), (25, "R01375", "Китайский юань"), (26, "R01390", "Кувейтский динар"), (27, "R01405", "Латвийский лат"), (28, "R01420", "Ливанский фунт"), (29, "R01435", "Литовский лит"), (30, "R01435", "Литовский талон"), (31, "R01500", "Молдавский лей"), (32, "R01510", "Немецкая марка"), (33, "R01510A", "Немецкая марка"), (34, "R01523", "Нидерландский гульден"), (35, "R01535", "Норвежская крона"), (36, "R01565", "Польский злотый"), (37, "R01570", "Португальский эскудо"), (38, "R01585", "Румынский лей"), (39, "R01585F", "Румынский лей"), (40, "R01589", "СДР (специальные права заимствования)"), (41, "R01625", "Сингапурский доллар"), (42, "R01665A", "Суринамский доллар"), (43, "R01670", "Таджикский сомони"), (44, "R01670B", "Таджикский рубл"), (45, "R01700J", "Турецкая лира"), (46, "R01710", "Туркменский манат"), (47, "R01710A", "Новый туркменский манат"), (48, "R01717", "Узбекский сум"), (49, "R01720", "Украинская гривна"), (50, "R01720A", "Украинский карбованец"), (51, "R01740", "Финляндская марка"), (52, "R01750", "Французский франк"), (53, "R01760", "Чешская крона"), (54, "R01770", "Шведская крона"), (55, "R01775", "Швейцарский франк"), (56, "R01790", "ЭКЮ"), (57, "R01795", "Эстонская крона"), (58, "R01804", "Югославский новый динар"), (59, "R01810", "Южноафриканский рэнд"), (60, "R01815", "Вон Республики Корея"), (61, "R01820", "Японская иена")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB20364C81');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE exchange_rate');
    }
}
