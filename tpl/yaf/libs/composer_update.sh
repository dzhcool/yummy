#!/bin/bash

SCRIPT_PATH=`dirname $0`
cd $SCRIPT_PATH

echo "[composer] begin update..."
if [ ! -f "$SCRIPT_PATH/composer.json" ]; then
    exit 0;
fi

composer update --prefer-dist
# find vendor/ -type d -name ".git" -exec rm -rf {} \;
echo "[composer] success!"
exit 0;
