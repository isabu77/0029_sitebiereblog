#!/bin/bash

if [ -e .env ]; then
    source .env
else
    echo "Please set up your .env file before starting your environment."
    exit 1
fi

docker-compose build

docker-compose -f docker-compose.yml up -d

sleep 4;

docker exec blog composer update

docker exec blog php commande/createsql.php

echo
echo "#-----------------------------------------------------------"
echo "#"
echo "# Please check your browser to see if it is running, use your"
echo "#"
echo "#-----------------------------------------------------------"
echo

exit 0
