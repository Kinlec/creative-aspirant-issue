include .env

default: up

up:
	@echo "Starting up containers for $(PROJECT_NAME)..."
	docker-compose pull
	docker-compose up -d --remove-orphans

build:
	@echo "Building up containers for $(PROJECT_NAME)..."
	docker-compose up -d --build

down: stop

stop:
	@echo "Stopping containers for $(PROJECT_NAME)..."
	@docker-compose stop

prune:
	@echo "Removing containers for $(PROJECT_NAME)..."
	@docker-compose down -v $(filter-out $@,$(MAKECMDGOALS))

sh:
	@docker exec -it $$(docker ps -aqf "name=$(PROJECT_NAME)_app_1") /bin/sh

restart:
	@docker-compose stop
	docker-compose pull
	docker-compose up -d --remove-orphans

rebuild:
	@docker-compose stop
	docker-compose up -d --build