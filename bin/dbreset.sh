#!/bin/bash

if [ -f ../.env ]; then
  export $(echo $(cat ../.env | sed 's/#.*//g' | xargs) | envsubst)
fi

sudo mysql --host="$DB_HOST" --user="$DB_USERNAME" --password="$DB_PASSWORD" < $(cd ../database/; pwd)/structure.sql
sudo mysql --host="$DB_HOST" --user="$DB_USERNAME" --password="$DB_PASSWORD" < $(cd ../database/; pwd)/imports.sql

echo "Database restored";
