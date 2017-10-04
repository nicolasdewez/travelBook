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
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(APP):9000 ddn0/wait 2> /dev/null | $(call $(PRINT),READY,$(COLOR_READY))
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(WEB):80 ddn0/wait 2> /dev/null | $(call $(PRINT),READY,$(COLOR_READY))
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(DB):5432 ddn0/wait 2> /dev/null | $(call $(PRINT),READY,$(COLOR_READY))
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(RABBITMQ):5672 ddn0/wait 2> /dev/null | $(call $(PRINT),READY,$(COLOR_READY))
	@docker run --rm --net=$(NETWORK) -e TIMEOUT=30 -e TARGETS=$(MAILER):1025 ddn0/wait 2> /dev/null | $(call $(PRINT),READY,$(COLOR_READY))

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
	@$(COMPOSE) down -v --remove-orphans | $(call $(PRINT),DOWN,$(COLOR_INSTALL))

.PHONY: destroy
destroy: stop rm ## Stop and remove containers

.PHONY: recreate
recreate: destroy up ## Recreate containers



.PHONY: queue-open
queue-open: ## Open the queue admin
	@xdg-open http://$(RABBITMQ).$(NETWORK):15672/ > /dev/null

.PHONY: queue-purge
queue-purge: ## Purge queue (ie. make queue-purge name="registration")
ifndef name
	@echo "To use the 'queue-purge' target, you MUST add the 'name' argument"
	exit 1
endif
	@$(EXEC) $(APP) bin/console rabbitmq:purge $(name) --no-interaction



.PHONY: mailer-open
mailer-open: ## Open the mailer
	@xdg-open http://$(MAILER).$(NETWORK):1080/ > /dev/null

.PHONY: mailer-send
mailer-send: ## Send emails
	@$(EXEC) $(APP) bin/console swiftmailer:spool:send

.PHONY: mailer-test
mailer-test: ## Send a test email
	@$(EXEC) $(APP) bin/console swiftmailer:email:send --from=from@travelbook.com --to=to@travelbook.com --subject=test --body="It's a test !" --no-interaction



.PHONY: check
check: check-security check-schema check-twig check-yaml check-php-cs ## Run all checks

.PHONY: check-security
check-security: ## Run security checker
	@$(EXEC) $(APP) bin/console security:check

.PHONY: check-schema
check-schema: db-validate

.PHONY: check-twig
check-twig: ## Run lint-twig
	@$(EXEC) $(APP) bin/console lint:twig templates/

.PHONY: check-yaml
check-yaml: ## Run lint-yaml
	@$(EXEC) $(APP) bin/console lint:yaml config/
	@$(EXEC) $(APP) bin/console lint:yaml translations/

.PHONY: check-php-cs
check-php-cs: ## Run php-cs-fixer
	@$(EXEC) $(APP) vendor/bin/php-cs-fixer fix -v --dry-run --diff --config=.php_cs.dist



.PHONY: app-open
app-open: ## Open the browser
	@xdg-open http://$(WEB).$(NETWORK)/ > /dev/null

.PHONY: app-exec
app-exec: ## Open a shell in the application container (options: user [www-data], cmd [bash], cont [`app`])
	$(eval cont ?= $(APP))
	$(eval user ?= www-data)
	$(eval cmd ?= bash)
	@$(COMPOSE) exec --user $(user) $(cont) $(cmd)

.PHONY: app-ass-comp
app-ass-comp: ## Compile assets
	$(eval env ?= dev)
	@$(EXEC) $(APP) ./node_modules/.bin/encore $(env)

.PHONY: app-php-cs
app-php-cs: ## Run php-cs-fixer to fix
	@$(EXEC) $(APP) vendor/bin/php-cs-fixer fix -v --diff --config=.php_cs.dist

.PHONY: app-workflow
app-workflow: ## Dump workflow (ie. make app-workflow name="registration")
ifndef name
	@echo "To use the 'workflow' target, you MUST add the 'name' argument"
	exit 1
endif
	@mkdir -p build
	@$(EXEC) $(APP) bin/console workflow:dump $(name) | dot -Tpng -o build/workflow-$(name).png

 .PHONY: app-changelog
app-changelog: ## Generate changelog (ie. make app-changelog from="v1.0.0" to="v1.1.0")
ifndef from
	@echo "To use the 'app-changelog' target, you MUST add the 'from' argument"
	exit 1
endif
ifndef to
	@echo "To use the 'app-changelog' target, you MUST add the 'to' argument"
	exit 1
endif
	@echo -e "## $(to)\n" > CHANGELOG_NEW.md
	@git log $(from)...$(to) --no-merges --pretty=format:'* %s [commit](https://github.com/nicolasdewez/travelBook/commit/%H)' >> CHANGELOG_NEW.md
	@echo -e  "\n" >> CHANGELOG_NEW.md
	@head -n 2 CHANGELOG.md > CHANGELOG_START.md
	@tail -n +2 CHANGELOG.md > CHANGELOG_END.md
	@cat CHANGELOG_START.md CHANGELOG_NEW.md CHANGELOG_END.md > CHANGELOG.md
	@rm CHANGELOG_*.md

.PHONY: app-clear
app-clear: ## Clear cache & logs
	rm -rf var/cache/* var/logs/*

.PHONY: app-cache-wmp
app-cache-wmp: app-clear ## Clear cache & warmup
	@test -f bin/console && bin/console cache:warmup || echo "cannot warmup the cache (needs symfony/console)"

.PHONY: app-reset
app-reset: down app-clear ## Reset application
	rm -rf vendor/ app/bootstrap.php.cache node_modules/



.PHONY: db-exec
db-exec: ## Run db cli (options: db_name [`travelbook`])
	$(eval db_name ?= $(DB_NAME))
	@$(COMPOSE) exec $(DB) psql -U travelbook

.PHONY: db-diff
db-diff: ## Run doctrine migrations diff
	@$(EXEC) $(APP) bin/console doctrine:migrations:diff

.PHONY: db-migrate
db-migrate: ## Run doctrine migrations
	@$(EXEC) $(APP) bin/console doctrine:migrations:migrate --no-interaction

.PHONY: db-validate
db-validate: ## Run doctrine validation
	@$(EXEC) $(APP) bin/console doctrine:schema:validate



.PHONY: tests-phpunit
tests-phpunit: ## Run phpunit test suite (options: coverage [true])
    ifeq ("$(coverage)","true")
		@$(EXEC) $(APP) vendor/bin/phpunit --coverage-html build/html
    else
		@$(EXEC) $(APP) vendor/bin/phpunit
    endif



.PHONY: anal-phpmetrics
anal-phpmetrics: ## Run phpmetrics
	@$(EXEC) $(APP) vendor/bin/phpmetrics src --git --report-html=build/phpmetrics



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