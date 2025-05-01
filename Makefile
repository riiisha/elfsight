EXEC = docker exec -w /app elfsight-php
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
SYMFONY_CONSOLE = $(PHP) bin/console

## —— 🔥 App ——
init: ## Инициализация проекта
	$(MAKE) start
	$(MAKE) composer-install
	@/bin/echo "Приложение доступно по адресу: http://127.0.0.1:8000/."

cache-clear: ## Очистка кэша
	$(SYMFONY_CONSOLE) cache:clear

analyze: ## Статический анализ src
	$(EXEC) vendor/bin/phpstan analyse src

## —— 🐳 Docker ——
start: ## Запуск приложения
	$(MAKE) docker-start

docker-start:
	docker-compose up -d --build

stop: ## Остановка приложения
	docker-compose stop
	@/bin/echo "Контейнеры остановлены."

## —— 🎻 Composer ——
composer-install: ## Установка зависимостей
	$(COMPOSER) install

composer-update: ## Обновление зависимостей
	$(COMPOSER) update

## —— 📊 Database ——
database-init: ## Инициализация базы данных
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate

database-create: ## Создание базы данных
	$(SYMFONY_CONSOLE) doctrine:database:create --if-not-exists

database-drop: ## Удаление базы данных
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists

database-migration: ## Создание миграции
	$(SYMFONY_CONSOLE) make:migration

database-migrate: ## Применение миграций
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction


## —— 🛠️  Others ——
help: ## Список команд
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
