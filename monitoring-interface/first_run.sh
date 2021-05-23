#!/bin/bash
docker volume create --name=monitoring_mysql
docker-compose run --rm php composer update --prefer-dist
docker-compose run --rm php composer install    
docker-compose run --rm php chown www-data:www-data -R /app/web/assets
docker-compose up -d
echo "Waiting for the containers to start"
sleep 20
docker-compose run --rm php yii migrate
