#!/bin/sh
[ $# -lt 1 ] && echo "Hi! but you must input env: d -> development; t -> testing; p -> production" && exit 0
if [ $1 = "d" ]; then
    echo "start development api"
    NODE_ENV=development supervisor --max_old_space_size=2048 ./bin/www
fi
if [ $1 = "t" ]; then
    echo "start testing api"
    NODE_ENV=testing supervisor --max_old_space_size=2048 ./bin/www
fi
if [ $1 = "p" ]; then
    echo "start production api"
    NODE_ENV=production supervisor --max_old_space_size=2048 ./bin/www
fi
