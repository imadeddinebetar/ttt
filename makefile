PROJECT_NAME = tcrcm
COMPOSE_FILE = docker/docker-compose.yml

up:
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) up -d

down:
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) down

clean:
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) stop
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) rm -f -v
	docker system prune -f --volumes

build:
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) build

bash:
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) exec app sh

log: 
	docker compose -f $(COMPOSE_FILE) -p $(PROJECT_NAME) logs app

restart: down up
