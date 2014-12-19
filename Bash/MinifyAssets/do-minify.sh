#! /bin/sh
YUICOMPRESS="java -jar yuicompressor-2.4.8.jar --charset utf-8"
TOUCH=touch
AWK=awk
RENAME=mv
COPY=cp
REMOVE=rm

if [ -n "$1" ]; then
    FULL_NAME="$1.FULL"
    MINIFIED_NAME="$1"
    EXTENSION=`echo "$MINIFIED_NAME" | sed 's/^.*\.//'`

    $RENAME "$MINIFIED_NAME" "$FULL_NAME"

    if [ $EXTENSION = "css" ]; then
        $YUICOMPRESS --type css "$FULL_NAME" > "$MINIFIED_NAME"
    fi

    if [ $EXTENSION = "js" ]; then
        $YUICOMPRESS --type js "$FULL_NAME" > "$MINIFIED_NAME"
    fi

	DATA_PLAIN=`stat --format "%s %Y" "$MINIFIED_NAME"`
    PLAIN_SIZE=`echo "$DATA_PLAIN" | $AWK '{ print $1}'`

    if [ $PLAIN_SIZE -eq 0 ]; then
    	$REMOVE "$MINIFIED_NAME"
    	$COPY "$FULL_NAME" "$MINIFIED_NAME"
        echo "File $MINIFIED_NAME NOT minified"
        exit 0;
    fi

    $TOUCH -r "$FULL_NAME" "$MINIFIED_NAME"
    echo "File $MINIFIED_NAME backuped and minified."
fi