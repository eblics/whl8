#!/bin/bash
[ $# -lt 1 ] && echo "Hi! but you must input env: d -> development; t -> testing; p -> production" && exit 0
#if [ $1 = "d" ]; then
#    CI_ENV=development php /var/www/whl8.cn/php_web/shop/index.php index jssdk >> /var/log/whl8.cn/refresh.log
#    exit 0
#fi
#if [ $1 = "t" ]; then
#    CI_ENV=testing php /var/www/whl8.cn/php_web/shop/index.php index jssdk >> /var/log/whl8.cn/refresh.log
#    exit 0
#fi
if [ $1 = "p" ]; then
    #CI_ENV=production php /var/www/whl8.cn/php_web/shop/index.php index jssdk >> /var/log/whl8.cn/refresh.log
    su www-data -c 'CI_ENV=production php /var/www/whl8.cn/php_web/manager/index.php wx3rd updateWxToken'
    exit 0
fi
