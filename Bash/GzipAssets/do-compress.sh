#! /bin/sh
MIN_FILE_SIZE=100
GZIP="gzip -9 -c -n"
AWK=awk
TOUCH=touch

if [ -n "$1" ]; then
    GZIPED_FILE_NAME="$1.gz"
    DATA_PLAIN=`stat --format "%s %Y" "$1"`
    PLAIN_SIZE=`echo "$DATA_PLAIN" | $AWK '{ print $1}'`
    PLAIN_MTIME=`echo "$DATA_PLAIN" | $AWK '{ print $2}'`

    if [ $PLAIN_SIZE -lt $MIN_FILE_SIZE ]; then
        echo "Ignoring file $1: its size ($PLAIN_SIZE) is less than $MIN_FILE_SIZE bytes"
        exit 0;
    fi

    if [ -f "$GZIPED_FILE_NAME" ]; then
        GZIPPED_MTIME=`stat --format "%Y" "$GZIPED_FILE_NAME"`
        if [ $GZIPPED_MTIME -eq $PLAIN_MTIME ]; then
            echo "Ignoring file $1: there is a compressed file $GZIPED_FILE_NAME with the same modification time"
            exit 0
        fi
    fi

    $GZIP "$1" > "$GZIPED_FILE_NAME"
    $TOUCH -r "$1" "$GZIPED_FILE_NAME"
    echo "Compressed $1 to $GZIPED_FILE_NAME"
fi