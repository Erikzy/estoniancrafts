.PHONY: install update clean dev load assets optimize setup

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  install	to make a Composer install"
	@echo "  update 	to make a Composer update then a Bower update"
	@echo "  clean  	to remove and warmup cache"
	@echo "  dev    	to start Built-in web server of PHP"
	@echo "  bower  	to make a Bower install"
#	@echo "  load   	to load fixtures"
	@echo "  webr   	to install web resources"
	@echo "  webr-w 	to work with web resources"
#	@echo "  optimize	to optimize sandbox"
#	@echo "  check  	run default symfony check"
	@echo "  setup  	set up project first time"
	@echo "  db-dump	dump db changes"
	@echo "  db-update	update db"

install:
	php composer.phar install
#	bower install

update:
	php composer.phar update
#	bower update

clean:
	rm -rf wp-content/cache/*

#load:
#       php bin/load_data.php

webr:	
	php bin/console we:webresource:deploy

webr-w:
	php bin/console we:webresource:watch

db-dump:
	php bin/console doctrine:schema:update --dump-sql 

db-update:
	php bin/console doctrine:schema:update --force