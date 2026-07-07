current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
workspace-dir := $(abspath $(current-dir)/..)
project-dir := $(notdir $(patsubst %/,%,$(current-dir)))

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
	@docker run --rm $(INTERACTIVE) --volume $(workspace-dir):/workspace --workdir /workspace/$(project-dir) --user $(id -u):$(id -g) \
		composer:2.5.8 $(CMD) \
			--ignore-platform-reqs \
			--no-ansi

test: composer-install
	docker run --rm -v $(workspace-dir):/workspace -w /workspace/$(project-dir) $(IMAGE) vendor/bin/phpunit $(FILTER_TEST_OPTIONS);
