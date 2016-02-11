#!/bin/bash

CURRENT_DIR=/var/www/euat
git pull
php ././../composer.phar install
php app/console assets:install --symlink
php app/console assetic:dump --env=prod
php app/console doctrine:schema:update --force
rm -R app/cache/*
rm -R app/logs/*
echo "--------------------------------------------------------------------------"
echo "Installation complete"
echo "--------------------------------------------------------------------------"
