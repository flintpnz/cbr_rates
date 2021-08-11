# cbr_rates

1. Склонировать репозиторий в рабочую директорию
```bash
git clone https://github.com/flintpnz/cbr_rates_docker.git
```
2. Склонировать репозиторий в директорию cbr_rates_docker/cbr_rates
```bash
git clone https://github.com/flintpnz/cbr_rates.git
```
4. В .env установить переменные
```bash
APP_ENV=prod
APP_SECRET=SECRET
DATABASE_URL="mysql://DBUSER:DBUSERPASSWORD@DB:3306/cbr_rates_db?serverVersion=5.7"
```
5. Создать директорию var
6. Выполнить настройки из репозитория
https://github.com/flintpnz/cbr_rates_docker.git
7. Импортировать данные в БД
```bash
//docker exec -i cbr_rates_mysql mysql -u root -p < /dump/cbr_rates_db.sql
```
8. В консоли контейнера cbr_rates_php
```bash
composer install --optimize-autoloader
```
9. Открыть в браузере http://domain.local:35000