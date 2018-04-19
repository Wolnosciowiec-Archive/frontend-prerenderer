#!/bin/bash

chown node:node /www/frontend-prerenderer/data -R
exec /entrypoint-php-node.sh
