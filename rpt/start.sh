#!/bin/sh
[ $# -lt 1 ] && echo "Hi! but you must input env: d -> development; t -> testing; p -> production" && exit 0
if [ $1 = "d" ]; then
    echo "start development rpt"
    NODE_ENV=development node app.js
fi
if [ $1 = "t" ]; then
    echo "start testing rpt"
    NODE_ENV=testing node app.js
fi
if [ $1 = "p" ]; then
    echo "start production rpt"
    NODE_ENV=production node app.js
fi