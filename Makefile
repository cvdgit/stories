init: docker-down-clear site-clear docker-pull docker-build docker-up site-init site-ready

down: docker-down
up: docker-up
test: site-test site-fixtures

docker-down-clear:
	docker compose down -v --remove-orphans

docker-down:
	docker compose down --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

docker-up:
	docker compose up -d

site-init: site-permissions \
		   site-composer-install \
		   site-assets-install \
		   site-wait-db \
		   site-migrations \
		   site-rbac \
		   site-fixtures \
		   site-test-generate \
		   site-assets-build

site-clear:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'rm -rf .ready public_html/assets/* public_html/admin/assets/* public_html/build/* api/runtime/* frontend/runtime/* console/runtime/* backend/runtime/* public_html/slides_file/* public_html/slides/* public_html/upload/mental-map/*'

site-permissions:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'mkdir -p public_html/build && chmod 777 public_html/assets public_html/admin/assets public_html/build api/runtime frontend/runtime console/runtime backend/runtime public_html/slides_file public_html/slides public_html/slides_cover public_html/slides_cover/list public_html/slides_cover/story public_html/test_images public_html/upload public_html/slides_video public_html/upload/testing public_html/upload/mental-map public_html/game public_html/game/arch public_html/photo'

site-composer-install:
	docker compose run --rm site composer install

site-ready:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine touch .ready

site-assets-install:
	docker compose run --rm site-node-cli npm install

site-assets-build:
	docker compose run --rm site-node-cli npm run build

site-wait-db:
	docker compose run --rm site wait-for-it site-mysql:3306 -t 30

site-migrations:
	docker compose run --rm site composer console migrate -- --interactive=0

site-rbac:
	docker compose run --rm site composer console rbac/init
	docker compose run --rm site composer console edu_console/rbac/init

site-fixtures:
	docker compose run --rm site composer console fixture/load '*' -- --interactive=0
	docker compose run --rm site composer console fixture '*' -- --namespace='modules\edu\fixtures' --interactive=0
	docker compose run --rm site composer console cache/flush cache -- --interactive=0
	#docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'rm -rf public_html/upload/* && cp -rf demo/upload/* public/upload'
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'find public_html/upload -type d -exec chmod 777 {} \;'
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'find public_html/upload -type f -exec chmod 666 {} \;'

site-test-generate:
	docker compose run --rm site composer test build

site-test:
	docker compose run --rm site composer test run

site-cert:
	mkcert -cert-file=./docker/apache-php/cert/frontend.crt -key-file=./docker/apache-php/cert/frontend.key local.wikids
