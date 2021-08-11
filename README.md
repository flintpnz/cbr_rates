# cbr_rates

1. Склонировать репозиторий в директорию cbr_rates_docker
```bash
git clone https://github.com/flintpnz/cbr_rates.git
```
2. Импортировать данные в БД
```bash
//docker exec -i cbr_rates_mysql mysql -u root -p < /dump/cbr_rates_db.sql
```
3. В .env установить переменные
```bash
APP_ENV=prod
APP_SECRET=
DATABASE_URL="mysql://DBUSER:DBUSERPASSWORD@DB:3306/cbr_rates_db?serverVersion=5.7"
```