#! /bin/sh

EXTENSIONS="jpg|jpeg|png|gif"

if [ -z "$1" ]; then
    DIR="`pwd`"
else
    DIR="$1"
fi

find $DIR -type f -regextype posix-egrep -regex ".*\.($EXTENSIONS)\$" -exec `dirname $0`/do-imgopt.sh '{}' \;

# if you want delete backuped images ( with extension "IMG_FULL"):
# find $DIR -name '*.IMG_FULL' -delete