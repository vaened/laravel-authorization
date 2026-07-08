current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
project-dir := $(abspath $(patsubst %/,%,$(current-dir)))

.PHONY: build clean deps composer-install composer-update composer-require composer-require-module

IMAGE=$(notdir $(patsubst %/,%,$(current-dir)))

build: deps
	docker build -t $(IMAGE) .

clean:
	docker rmi $(IMAGE)

deps: composer-install

composer-install: CMD=install

composer-update: CMD=update

composer-require: CMD=require
composer-require: INTERACTIVE=-ti --interactive

composer composer-install composer-update composer-require composer-require-module:
	@docker run --rm $(INTERACTIVE) --volume $(project-dir):/app --workdir /app --user $(id -u):$(id -g) \
		composer:2.5.8 $(CMD) \
			--ignore-platform-reqs \
			--no-ansi

test: composer-install
	docker run --rm -v $(project-dir):/app -w /app $(IMAGE) vendor/bin/phpunit $(FILTER_TEST_OPTIONS);
