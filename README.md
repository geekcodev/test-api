# Запуск и разработка

## Локальный запуск

### 1) Подготовка `.env`

Создайте файл `.env` в корне проекта на основе `.env.example` и измените/заполните `HTTP_PORT`, `DB_PASSWORD`, `REDIS_PASSWORD` и остальные при необходимости.

### 2) Установка зависимостей

```bash
docker compose run --rm app composer install
```

### 3) Генерация `APP_KEY`

```bash
docker compose run --rm app php /var/www/html/artisan key:generate --show
```

Скопируйте значение и вставьте его в `.env` в переменную `APP_KEY`.

### 5) Сборка и запуск контейнеров

```bash
docker compose up -d --build
```

### 6) Миграции и заполнение БД

```bash
docker compose exec app php /var/www/html/artisan migrate
```

```bash
docker compose exec app php /var/www/html/artisan db:seed
```

### 7) Тестирование

```bash
docker compose exec app php /var/www/html/artisan test
```
