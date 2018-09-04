<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

defined('ROOTURL')             OR define('ROOTURL', 'lsa0.cn');

// ============================= Added by shizq start =============================
defined('ROLE_CONSUMER')       OR define('ROLE_CONSUMER', 0);
defined('ROLE_WAITER')         OR define('ROLE_WAITER', 1);
defined('ROLE_SALESMAN')       OR define('ROLE_SALESMAN', 2);

defined('ACTION_SCAN') OR define('ACTION_SCAN', 0);
defined('ACTION_TRANS') OR define('ACTION_TRANS', 1);

defined('OBJ_TYPE_RED_PACKET') OR define('OBJ_TYPE_RED_PACKET', 0);
defined('OBJ_TYPE_HAPPY_COIN') OR define('OBJ_TYPE_HAPPY_COIN', 1);
defined('OBJ_TYPE_CARD') OR define('OBJ_TYPE_CARD', 2);

defined('SCAN_RES_TRANSFER') OR define('SCAN_RES_TRANSFER', 1);
defined('SCAN_RES_NORMAL') OR define('SCAN_RES_NORMAL', 0);

defined('TICKET_SCANED') OR define('TICKET_SCANED', 1);
defined('TICKET_NOT_SCANED') OR define('TICKET_NOT_SCANED', 0);

defined('TICKET_CONFIRMED') OR define('TICKET_CONFIRMED', 1);
defined('TICKET_NOT_CONFIRMED') OR define('TICKET_NOT_CONFIRMED', 0);

defined('USER_CARDS_STATUS_NORMAL') OR define('USER_CARDS_STATUS_NORMAL', 0);
defined('USER_CARDS_STATUS_TRANSED') OR define('USER_CARDS_STATUS_TRANSED', 1);
defined('USER_CARDS_STATUS_SETTLED') OR define('USER_CARDS_STATUS_SETTLED', 2);
// ============================= Added by shizq end ===============================
/*
 * |--------------------------------------------------------------------------
 * | 公共全局自定义
 * |--------------------------------------------------------------------------
 * |
 */
defined('PRODUCT_NAME') or define('PRODUCT_NAME', '红码'); // 本产品名称
defined('SYSTEM_NAME') or define('SYSTEM_NAME', '管理平台'); // 本系统名称


/**
 * Added by shizq
 *
 */
defined('ROLE_ADMIN_MASTER') OR define('ROLE_ADMIN_MASTER', 0);
defined('ROLE_ADMIN_PRODUCER') OR define('ROLE_ADMIN_PRODUCER', 1);
defined('ROLE_ADMIN_ACTIVITY_MATER') OR define('ROLE_ADMIN_ACTIVITY_MATER', 2);
defined('ROLE_ADMIN_ACTIVITY_EXECUTOR') OR define('ROLE_ADMIN_ACTIVITY_EXECUTOR', 3);
defined('ROLE_ADMIN_NORMAL') OR define('ROLE_ADMIN_NORMAL', 4);
defined('ROW_STATUS_NORMAL') OR define('ROW_STATUS_NORMAL', 0);
defined('ROW_STATUS_DISABLE') OR define('ROW_STATUS_DISABLE', 1);
defined('ROW_STATUS_FREEZED') OR define('ROW_STATUS_FREEZED', 2);

defined('SMS_TEMPLATE_REG') OR define('SMS_TEMPLATE_REG', 'SMS_7895082');
