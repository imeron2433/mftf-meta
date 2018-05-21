#!/bin/sh

set -e
if [ -z "$1" ]
   then
      echo "Must pass in path to magento2ce parent dir as arg"
      exit 1
fi

if [ ! -d "app/vendor" ] && [[ "$(docker images -q mftf/meta 2> /dev/null)" == "" ]]
   then
      echo "You must first run composer install from the 'app' dir in order to build the project from scratch"
      exit 1
fi

export MAGENTO_BASE_PATH=$1
docker-compose -f docker-compose.yml up -d
