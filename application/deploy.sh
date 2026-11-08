#!/usr/bin/env bash
composer self-update
composer install
php bin/console doctrine:migrations:migrate -n
php bin/console cache:clear
npm install
npm run build
# Geänderte Seiten per IndexNow an Bing & Co. melden (Fehler brechen den Deploy nicht ab)
php bin/console app:indexnow:submit --no-interaction || true
