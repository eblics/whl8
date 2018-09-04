#!/bin/bash

read -p "请输入要更新的内容（php|h5|js|css|img）:" content

if [ $content = "php" ]; then 
	rm -rf /var/www/lsa0.cn/php_web/application
	cp -rf /var/nfs4/source/php_web/application /var/www/lsa0.cn/php_web/application
	echo "application updated ok."
fi

if [ $content = "h5" ]; then 
	read -p "请输入要更新的h5:" h5name

	if [ ! -d  "/var/www/lsa0.cn/php_web/mobile/h5/$h5name" ]; then
		rm -rf /var/www/lsa0.cn/php_web/mobile/h5/$h5name
	fi
	if [ ! -d /var/nfs4/source/php_web/mobile/h5/$h5name ]; then
		echo "h5 -> $h5name does not exists" && exit 0
	fi
	cp -rf /var/nfs4/source/php_web/mobile/h5/$h5name /var/www/lsa0.cn/php_web/mobile/h5/$h5name
	echo "h5 $h5name updated ok."
fi

if [ $content = "js" ]; then 
	rm -rf /var/www/lsa0.cn/php_web/mobile/static/js
	cp -rf /var/nfs4/source/php_web/mobile/static/js /var/www/lsa0.cn/php_web/mobile/static/js
	echo "js updated ok."
fi

if [ $content = "css" ]; then 
	rm -rf /var/www/lsa0.cn/php_web/mobile/static/css
	cp -rf /var/nfs4/source/php_web/mobile/static/css /var/www/lsa0.cn/php_web/mobile/static/css
	echo "css updated ok."
fi