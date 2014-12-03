#!/bin/bash

IFACE="$1"
ESSID="$2"

SCAN=$( \
sudo /sbin/iwlist $IFACE scan 2>&1 | /bin/grep -v "^$IFACE" | grep -v "^$" | \
    /bin/sed -e "s/^\ *//" \
        -e "s/^Cell [0-9]\+ - /#/" \
        -e "s/^#Address: /#AP=/" \
        -e "s/^Quality:\([0-9]\+\)\/.*$/QUALITY=\1/" \
        -e "s/^.*Channel \([0-9]\+\).*$/CHANNEL=\1/" \
        -e "s/^ESSID:/ESSID=/" \
        -e "s/^Mode:/MODE=/" \
        -e "s/^Encryption key:/ENC=/" \
        -e "s/^[^#].*:.*//" | \
    /usr/bin/tr "\n#" "|\n" \
)

/bin/echo -e "$SCAN"| /bin/grep "$ESSID"
exit 1
