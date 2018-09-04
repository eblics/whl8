#mysqld_safe --user=mysql --pid-file=/var/run/mysqld/mysqld.pid --socket=/var/run/mysqld/mysqld.sock --port=3306 --basedir=/usr --datadir=/var/lib/mysql
import os
import sys
import re;
size=5;
if sys.argv[1]=='create':
    for i in range(1,size):
        os.system('mysql_install_db --user=mysql --defaults-file=/etc/mysql/multi.conf --datadir=/var/lib/mysql{0}'.format(i))

if sys.argv[1]=='remove':
    for i in range(1,size):
        os.system('rm -r /var/lib/mysql{0}'.format(i))

if sys.argv[1]=='start':
    for i in range(1,size):
        os.system('mysqld_safe  --defaults-file=/etc/mysql/mysqld{0}.conf --pid-file=/var/run/mysqld/mysqld{0}.pid  --socket=/var/run/mysqld/mysqld{0}.sock --port={1} --basedir=/usr --datadir=/var/lib/mysql{0} &'.format(i,3306+i))
        #os.system('mysqld_safe  --defaults-file=/etc/mysql/multi.conf  --basedir=/usr --datadir=/var/lib/mysql{0}'.format(i))

if sys.argv[1]=='source':
    for i in range(1,size):
        os.system('mysql -S /var/run/mysqld/mysqld{0}.sock {1}<{2}'.format(i,sys.argv[2],sys.argv[3]))

if sys.argv[1]=='exec':
    for i in range(1,size):
        os.system("mysql -S /var/run/mysqld/mysqld{0}.sock {1} -e \"{2}\"".format(i,sys.argv[2],sys.argv[3]))

if sys.argv[1]=='stop':
    for i in range(1,size):
        os.system('mysqladmin -S /var/run/mysqld/mysqld{0}.sock shutdown'.format(i))

if sys.argv[1]=='conf':
    for i in range(1,size):
        f=open('mysqld{0}.conf'.format(i),'w')
        f.write('''[mysqld]
user		= mysql
pid-file	= /var/run/mysqld/mysqld{0}.pid
socket		= /var/run/mysqld/mysqld{0}.sock
log_error   =/var/log/mysql/error.log
server-id   ={1}
port		= {1}
basedir		= /usr
datadir		= /var/lib/mysql{0}
tmpdir		= /tmp
bind-address		= 0.0.0.0
binlog_format=STATEMENT
relay-log=mysqld{0}-relay-bin
max_allowed_packet	= 512M'''.format(i,3306+i));
        f.close();

if sys.argv[1]=='autorestore':
    re_binlog='File:\s*(\S+)'
    re_pos='Position:\s*(\d+)'
    if sys.argv[2]=='test':
        db='hls_test'
    else:
        db='hls_prod'
    os.system('python mysql.py conf')
    os.system('mv mysqld*.conf /etc/mysql/')
    os.system('python mysql.py create')
    os.system('python mysql.py start')
    out=os.popen("mysql -uroot -p1acctrue1 {0} -e 'flush tables with read lock;show master status\G'".format(db)).read()
    os.system('mysqldump -uroot -p1acctrue1 {0}>{0}.sql'.format(db))
    binlog_name=re.search(re_binlog,out).group(1)
    binlog_pos=re.search(re_pos,out).group(1)
    os.system("python mysql.py exec '' 'create database {0};'".format(db))
    os.system('python mysql.py source {0} {0}.sql'.format(db))
    os.system("python mysql.py exec '{0}' \"{1}\"".format(db,"change master to master_host='localhost',master_user='root',master_password='1acctrue1',master_log_file='{0}',master_log_pos={1};start slave;".format(binlog_name,binlog_pos)))
    #print "mysql exec '{0}' \"{1}\"".format(db,"change master to master_host='localhost',master_user='root',master='1acctrue1',master_log_file='{0}',master_log_pos={1};start slave;".format(binlog_name,binlog_pos))
    os.system("mysql -uroot -p1acctrue1 {0} -e 'unlock tables;'".format(db))


