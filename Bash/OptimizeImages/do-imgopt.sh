#! /bin/sh
OPTIMIZE_CMD="imgo"
COPY=cp

if [ -n "$1" ]; then
    FULL_NAME="$1.IMG_FULL"
    OPTIMIZED_NAME="$1"

    echo " "
    echo "File: $OPTIMIZED_NAME"

    $COPY "$OPTIMIZED_NAME" "$FULL_NAME"
    $OPTIMIZE_CMD "$OPTIMIZED_NAME"
fi