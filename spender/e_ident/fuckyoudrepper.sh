#!/bin/sh
#
# http://sourceware.org/ml/glibc-cvs/2010-q1/msg00198.html
# not used for anything for a decade before this patch
# not used for anything since the patch
# just drepper breaking PaX, ldd will report PaX ei_ident-marked binaries
# as invalid ELFs
#

if [ -f "$1" ]; then
GOTPLT=`readelf -e "$1" | grep .got.plt | head -n 1 | cut -b42- | cut -b-8`
cc -o fuck_you_drepper fuck_you_drepper.c
cp -p "$1" "$1.new"
cp -p "$1" "$1.old"
./fuck_you_drepper "$1" "$GOTPLT"
else
echo "usage: ./fuckyoudrepper /lib/ld-linux.so.2"
fi
