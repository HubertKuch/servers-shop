#!/bin/bash

sudo git clone https://github.com/HubertKuch/servers-shop.git /var/www/servers
cd /var/www/servers/
rm .env
cp ../.env.servers.production ./.env
composer install --ignore-platform-reqs
