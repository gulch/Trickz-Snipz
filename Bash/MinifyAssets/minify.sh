#! /bin/sh

EXTENSIONS="css|js"

if [ -z "$1" ]; then
    DIR="`pwd`"
else
    DIR="$1"
fi

find $DIR -type f -regextype posix-egrep -regex ".*\.($EXTENSIONS)\$" -exec `dirname $0`/do-minify.sh '{}' \;