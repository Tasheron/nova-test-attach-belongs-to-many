MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=test
MYSQL_CONTAINER_NAME=mysql-novatest

.PHONY: app

up:
	docker start $(MYSQL_CONTAINER_NAME)
down:
	docker stop $(MYSQL_CONTAINER_NAME)
run:
	php artisan serve

db:
	docker exec -it $(MYSQL_CONTAINER_NAME) bash -c "mysql -uroot -p$(MYSQL_ROOT_PASSWORD) $(MYSQL_DATABASE)"
dbs:
	docker exec -it $(MYSQL_CONTAINER_NAME) bash

css:
	npm run build

build:
	docker run -d --platform=linux/amd64 --name $(MYSQL_CONTAINER_NAME) -p 3306:3306 \
	-e MYSQL_ROOT_PASSWORD=$(MYSQL_ROOT_PASSWORD) \
	-e MYSQL_DATABASE=$(MYSQL_DATABASE) \
	mysql:5

install:
	cp .env.example .env \
	&& composer install \
	&& php artisan key:generate --ansi \
	&& php artisan migrate \
	&& php artisan db:seed \
	&& npm install \
	&& npm run build \
	&& cd nova-components/AttachBelongsToMany \
	&& composer install \
	&& npm install \
	&& npm run dev