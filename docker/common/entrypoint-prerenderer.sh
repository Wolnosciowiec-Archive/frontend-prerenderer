#!/bin/bash

chown node:node /www/frontend-prerenderer/data -R

# if the container has files mounted as a volume, then probably the composer and npm was not run yet
if [[ ! -d /www/frontend-prerenderer/vendor ]] || [[ ! -d /www/frontend-prerenderer/node_modules ]]; then
    cd /www/frontend-prerenderer && make deploy migrate
fi

exec /entrypoint-php-node.sh
