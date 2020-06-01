COMPOSER_ARGS += --no-interaction --no-progress --no-suggest

.PHONY: build
build: vendor

.PHONY: vendor
vendor: vendor/lock

vendor/lock: composer.json
	composer update $(COMPOSER_ARGS)
	touch vendor/lock

.PHONY: test
test:
	vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY: cs
cs:
	vendor/bin/phpcs $(PHPCS_ARGS)

.PHONY: fix
fix:
	vendor/bin/phpcbf

.PHONY: static-analysis
static-analysis:
	vendor/bin/phpstan analyse $(PHPSTAN_ARGS)

.PHONY: check
check: build cs static-analysis test

.PHONY: clean
clean: clean-vendor

.PHONY: clean-vendor
clean-vendor:
	rm -rf vendor composer.lock
