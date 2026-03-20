.PHONY: install test lint build ci clean

# Abhängigkeiten installieren
install:
	composer install

# Unit-Tests ausführen
test:
	vendor/bin/phpunit

# Code-Style prüfen
lint:
	vendor/bin/phpcs

# Code-Style-Fehler automatisch beheben (wo möglich)
fix:
	vendor/bin/phpcbf

# Plugin-ZIP bauen
build:
	bash bin/build.sh

# Alles: install → lint → test
ci: install lint test

# Build-Artefakte aufräumen
clean:
	rm -rf dist vendor
