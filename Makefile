SHELL = /bin/bash -o pipefail
.DEFAULT_GOAL := help

#################################
# Configuration
#################################

# Global
PROJECT ?= travelbook
APP = app
WEB = web
DB = db
DB_NAME = travelbook
RABBITMQ = rabbitmq
MAILER = mailer
NETWORK = travelbook
DEBUG = $(debug)

# Aliases
COMPOSE = $(ENV_VARS) docker-compose -p $(PROJECT) -f docker-compose.yml
EXEC = $(COMPOSE) exec -T --user=www-data
ENV_VARS = NETWORK=$(NETWORK) DEBUG=$(DEBUG)

# Project name must be compatible with docker-compose
override PROJECT := $(shell echo $(PROJECT) | tr -d -c '[a-z0-9]' | cut -c 1-55)

# Print output
# For colors, see https://en.wikipedia.org/wiki/ANSI_escape_code#Colors
INTERACTIVE := $(shell tput colors 2> /dev/null)
COLOR_UP = 3
COLOR_INSTALL = 6
COLOR_WAIT = 5
COLOR_STOP = 1
PRINT_CLASSIC = cat
PRINT_PRETTY = sed 's/^/$(shell printf "\033[3$(2)m[%-7s]\033[0m " $(1))/'
PRINT_PRETTY_NO_COLORS = sed 's/^/$(shell printf "[%-7s] " $(1))/'
PRINT = PRINT_CLASSIC


#################################
# Targets
#################################

.PHONY: start
start: pretty init-composer network up install ## Start containers & install application

.PHONY: up
up: ## Builds, (re)creates, starts containers
	@$(COMPOSE) up -d --remove-orphans 2>&1 | $(call $(PRINT),UP,$(COLOR_UP))

.PHONY: install
install: ready ## Install application
	@$(COMPOSE) exec $(DB) /usr/local/src/init.sh | $(call $(PRINT),INSTALL,$(COLOR_INSTALL))
	@$(EXEC) $(APP) bin/install | $(call $(PRINT),INSTALL,$(COLOR_INSTALL))

.PHONY: ready
ready: pretty ## Check if environment is ready
	@echo "[READY]" | $(call $(PRINT),READY,$(COLOR_READY))
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(APP):9000 ddn0/wait 2> /dev/null
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(WEB):80 ddn0/wait 2> /dev/null
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(DB):5432 ddn0/wait 2> /dev/null
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(RABBITMQ):5672 ddn0/wait 2> /dev/null
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(MAILER):1025 ddn0/wait 2> /dev/null

.PHONY: open-app
open-app: ## Open the browser
	@xdg-open http://$(WEB).$(NETWORK)/ > /dev/null

.PHONY: open-rabbitmq
open-rabbitmq: ## Open the admin rabbitmq
	@xdg-open http://$(RABBITMQ).$(NETWORK):15672/ > /dev/null

.PHONY: open-mailer
open-mailer: ## Open the mailer
	@xdg-open http://$(MAILER).$(NETWORK):1080/ > /dev/null

.PHONY: phpunit
phpunit: ## Run phpunit test suite
    ifeq ("$(coverage)","true")
		@$(EXEC) $(APP) vendor/bin/phpunit --coverage-html build/html
    else
		@$(EXEC) $(APP) vendor/bin/phpunit
    endif

.PHONY: phpmetrics
phpmetrics: ## Run phpmetrics
	@$(EXEC) $(APP) vendor/bin/phpmetrics src --git --report-html=build/phpmetrics

.PHONY: security-check
security-check: ## Run security-checker
	@$(EXEC) $(APP) bin/console security:check

.PHONY: php-cs-fixer
php-cs-fixer: ## Run php-cs-fixer
	@$(EXEC) $(APP) vendor/bin/php-cs-fixer fix -v --dry-run --diff --config=.php_cs.dist

.PHONY: lint-twig
lint-twig: ## Run lint-twig
	@$(EXEC) $(APP) bin/console lint:twig templates/

.PHONY: lint-yaml
lint-yaml: ## Run lint-yaml
	@$(EXEC) $(APP) bin/console lint:yaml config/
	@$(EXEC) $(APP) bin/console lint:yaml translations/

.PHONY: schema-validate
schema-validate: ## Run schema-validate
	@$(EXEC) $(APP) bin/console doctrine:schema:validate

.PHONY: checker
checker: security-check lint-twig lint-yaml schema-validate php-cs-fixer ## Run checker: security-check, lint-twig, lint-yaml, schema-validate, php-cs-fixer

.PHONY: php-cs-fixer-exec
php-cs-fixer-exec: ## Run php-cs-fixer
	@$(EXEC) $(APP) vendor/bin/php-cs-fixer fix -v --diff --config=.php_cs.dist

.PHONY: assets-compile
assets-compile: ## Compile assets
	$(eval env ?= dev)
	@$(EXEC) $(APP) ./node_modules/.bin/encore $(env)

.PHONY: queue-purge
queue-purge: ## Purge rabbitmq queue (ie. make purge-queue name="registration")
ifndef name
	@echo "To use the 'purge-queue' target, you MUST add the 'name' argument"
	exit 1
endif
	@$(EXEC) $(APP) bin/console rabbitmq:purge $(name) --no-interaction

.PHONY: mailer-test
mailer-test: ## Send a test email
	@$(EXEC) $(APP) bin/console swiftmailer:email:send --from=from@travelbook.com --to=to@travelbook.com --subject=test --body="It's a test !" --no-interaction

.PHONY: mailer-send
mailer-send: ## Send emails
	@$(EXEC) $(APP) bin/console swiftmailer:spool:send

.PHONY: migrate
migrate: ## Run doctrine migrations
	@$(EXEC) $(APP) bin/console doctrine:migrations:migrate --no-interaction

.PHONY: workflow
workflow: ## Dump workflow (ie. make workflow name="registration")
ifndef name
	@echo "To use the 'workflow' target, you MUST add the 'name' argument"
	exit 1
endif
	@mkdir -p build
	@$(EXEC) $(APP) bin/console workflow:dump $(name) | dot -Tpng -o build/workflow-$(name).png

.PHONY: exec
exec: ## Open a shell in the application container (options: user [www-data], cmd [bash], cont [`app`])
	$(eval cont ?= $(APP))
	$(eval user ?= www-data)
	$(eval cmd ?= bash)
	@$(COMPOSE) exec --user $(user) $(cont) $(cmd)

.PHONY: pgsql
pgsql: ## Run pgsql cli (options: db_name [`travelbook`])
	$(eval db_name ?= $(DB_NAME))
	@$(COMPOSE) exec $(DB) psql -U travelbook

.PHONY: ps
ps: ## List containers status
	@$(COMPOSE) ps

.PHONY: logs
logs: ## Dump containers logs
	@$(COMPOSE) logs -f

.PHONY: stop
stop: ## Stop containers
	@$(COMPOSE) stop 2>&1 | $(call $(PRINT),STOP,$(COLOR_INSTALL))

.PHONY: rm
rm: ## Remove containers
	@$(COMPOSE) rm --all -f 2>&1 | $(call $(PRINT),REMOVE,$(COLOR_INSTALL))

.PHONY: down
down: ## Stop and remove containers, networks, volumes
	@$(COMPOSE) down -v --remove-orphans

.PHONY: destroy
destroy: stop rm ## Stop and remove containers

.PHONY: recreate
recreate: destroy up ## Recreate containers

.PHONY: clear
clear: ## Clear cache & logs
	rm -rf var/cache/* var/logs/*

.PHONY: cache-warmup
cache-warmup: clear ## Clear cache & warmup
	@test -f bin/console && bin/console cache:warmup || echo "cannot warmup the cache (needs symfony/console)"

.PHONY: reset
reset: down clear ## Reset application
	rm -rf vendor/ app/bootstrap.php.cache node_modules/

.PHONY: init-composer
init-composer:
	@mkdir -p ~/.composer

.PHONY: network
network:
	@docker network create ${NETWORK} 2> /dev/null || true

.PHONY: pretty
pretty:
ifdef INTERACTIVE
	$(eval PRINT = PRINT_PRETTY)
else
	$(eval PRINT = PRINT_PRETTY_NO_COLORS)
endif
	@true

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'