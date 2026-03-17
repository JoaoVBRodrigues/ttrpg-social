#!/bin/sh
set -eu

cd /var/www/html

if [ ! -x node_modules/.bin/vite ]; then
    echo "Installing Node dependencies..."
    npm install
fi

exec "$@"
