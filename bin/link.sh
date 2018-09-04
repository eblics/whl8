#!/bin/sh

pwd=`pwd`
[ $# -lt 1 ] && echo "Please input the evn!" && exit 0

rm $pwd/files

if [ $1 = "prod" ]; then
  if [ ! -d "/var/nfs4" ]; then
    mkdir /var/nfs4
    mount -t nfs 10.45.234.170:/var/nfs4 /var/nfs4
    echo 'prod - mount disk success to 10.45.234.170'
  fi
  # ln -s /var/nfs4/files $pwd/files
elif [ $1 = "test" ]; then
  if [ ! -d "/var/nfs4/files" ]; then
    mkdir -p /var/nfs4/files
    mount -t nfs 10.30.145.182:/var/nfs4/files /var/nfs4/files
    echo 'test - mount disk success to 10.30.145.182'
  fi
  # ln -s /var/nfs4/files $pwd/files
else
  if [ ! -d "/var/nfs4/files" ]; then
    mkdir -p /var/nfs4/files
    mount -t nfs 10.30.145.182:/var/nfs4/files /var/nfs4/files
    echo 'dev - mount disk success to 10.30.145.182'
  fi
  # ln -s /var/nfs4/files/dev/files $pwd/files
fi

for d in 'merchant' 'mobile' 'opp'
do
  rm $pwd/$d/system
  # rm $pwd/$d/www/files
  rm $pwd/$d/application/libraries/common
  rm $pwd/$d/application/helpers/common
  rm $pwd/$d/application/models
  rm $pwd/$d/application/core
  rm $pwd/$d/application/config/development
  rm $pwd/$d/application/config/testing
  rm $pwd/$d/application/config/production

  ln -s $pwd/common/system       $pwd/$d/system
  # ln -s /var/nfs4/files          $pwd/$d/www/files
  ln -s $pwd/common/libraries    $pwd/$d/application/libraries/common
  ln -s $pwd/common/helpers      $pwd/$d/application/helpers/common
  ln -s $pwd/common/models       $pwd/$d/application/models
  ln -s $pwd/common/core         $pwd/$d/application/core
  ln -s $pwd/common/config/dev   $pwd/$d/application/config/development
  ln -s $pwd/common/config/test  $pwd/$d/application/config/testing
  ln -s $pwd/common/config/prod  $pwd/$d/application/config/production
done

echo "links create success"


# 聊天记录目录
rm $pwd/websocket/data/chat
ln -s $pwd/files/private/websocket/chat $pwd/websocket/data/chat
