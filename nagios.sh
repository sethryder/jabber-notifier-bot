#!/bin/bash
servers=( "" )

for i in "${servers[@]}"
do
        wget --timeout=5 -qO- http://$i/stat.php | sed "s/^/|$i,/" &
done
