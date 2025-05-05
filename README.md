<h1>Тестовое задание</h1>

## Выполните следующие команды в терминале для запуска проекта:

```bash
docker-compose up -d
docker exec -it elfsight-php composer install
```
```bash
docker exec -it elfsight-php php bin/console doctrine:database:create --if-not-exists
```
```bash
docker exec -it elfsight-php php bin/console doctrine:migrations:migrate --no-interaction
```
```bash
docker exec -it elfsight-php php bin/console app:import-episodes
```

## Доступные эндпоинты
```
GET /api/episodes — список всех эпизодов

GET /api/episode/{id} — информация об одном эпизоде

POST /api/episodes/{id}/reviews — добавить отзыв к эпизоду
```