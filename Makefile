run:
	docker-compose up

test:
	phpunit app/tests/

composer-validate:
	cd app && composer validate

phpcbf:
	phpcbf --standard=PSR12 app/public/ app/src/ app/tests/

phpcs:
	phpcs --standard=PSR12 app/public/ app/src/ app/tests/

security-advisories:
	cd app && composer require --dev "roave/security-advisories:dev-latest"

yamllint:
	yamllint -s -f github docker-compose.yml .github/workflows/*.yml
