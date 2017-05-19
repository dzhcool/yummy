#!/bin/bash
SCRIPT_PATH=`dirname $0`
cd $SCRIPT_PATH

echo "[composer] begin update..."

if [ ! -f "$SCRIPT_PATH/composer.json" ]; then
    echo "[composer] failed! absent composer.json."
    exit -1;
fi

/usr/local/php-5.6/bin/composer update --prefer-dist

find vendor/ -type d -name ".git" -exec rm -rf {} \;
echo "[composer] success!"
exit 0;
