#!/usr/bin/make
# Makefile readme (ru): <http://linux.yaroslavl.ru/docs/prog/gnu_make_3-79_russian_manual.html>
# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

SHELL = /bin/sh
RUN_APP_ARGS = --rm --user "$(shell id -u):$(shell id -g)"

.PHONY : help build install lint test shell clean
.DEFAULT_GOAL : help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build docker images, required for current package environment
	docker-compose build

install: clean ## Install php dependencies
	docker-compose run $(RUN_APP_ARGS) app composer update -n --prefer-dist --no-interaction

lint: ## Execute linters
	docker-compose run $(RUN_APP_ARGS) app composer lint

test: ## Execute php tests
	docker-compose run $(RUN_APP_ARGS) app composer test

shell: ## Start shell into container with php
	docker-compose run $(RUN_APP_ARGS) app sh

clean: ## Remove all dependencies and unimportant files
	-rm -rf ./composer.lock ./vendor ./coverage ./tests/temp
