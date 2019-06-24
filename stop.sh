#!/bin/bash

docker-compose stop
sleep 3;
docker-compose rm -f

echo
echo "---------------------------------------"
echo " container arrêté et supprimé "
echo "---------------------------------------"
echo

exit 0