PROJECT := football

dev:
	@composer install
	@docker-compose -p ${PROJECT} -f ops/docker/docker-compose.yml up -d


logs:
	@docker-compose -p ${PROJECT} -f ops/docker/docker-compose.yml logs

kill:
	@docker-compose -p ${PROJECT} -f ops/docker/docker-compose.yml kill
	@docker-compose -p ${PROJECT} -f ops/docker/docker-compose.yml rm -f

test:
	@./bin/phpunit

status:
	@docker-compose -f ops/docker/docker-compose.yml ps

