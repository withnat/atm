<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASEPATH', __DIR__);
define('BASEPATH_ASSETS', BASEPATH . DS . 'assets');
define('BASEPATH_LANGUAGES', BASEPATH . DS . 'languages');
define('BASEPATH_PACKAGES', BASEPATH . DS . 'packages');
define('BASEPATH_SYSTEM', BASEPATH . DS . 'system');
define('BASEPATH_TEMPLATES', BASEPATH . DS . 'templates');
define('BASEPATH_TMP', BASEPATH . DS . 'tmp');
define('RESIZE_DIR', 'resize');
define('RESIZE_DEFAULT_WIDTH', 300);
define('RESIZE_DEFAULT_HEIGHT', 300);

/*
define('DB_DRIVER', 'sqlite');
define('DB_HOST', 'localhost');
define('DB_SOCKET', '/var/run/mysqld/mysqld.sock');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8');
define('DB_NAME', 'vanda');
define('DB_PREFIX', 'vd_');
define('DB_USER', 'test');
define('DB_PASSWORD', 'none');
*/

define('DB_DRIVER', 'mysql');
define('DB_HOST', '127.0.0.1');

/*
For PDO error code 2002 (socket problem).
Try with 127.0.0.1 instead of localhost for the hostname

When the host name is set to "localhost", then the connection to 
the server is made thru a domain socket. If PDO_MYSQL is 
compiled against libmysqlclient then the location of the socket 
file is at libmysqlclient's compiled in location. If PDO_MYSQL is 
compiled against mysqlnd a default socket can be set thru the 
pdo_mysql.default_socket setting.
*/

//define('DB_SOCKET', '/var/run/mysqld/mysqld.sock');
//define('DB_SOCKET', '');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8');
define('DB_NAME', 'atm');
define('DB_PREFIX', 'vd_');
define('DB_USER', 'root');
define('DB_PASSWORD', 'none');

// define('DB_DRIVER', 'sqlsrv');
// define('DB_HOST', '10.0.2.15');
// define('DB_PORT', 1433);
// define('DB_CHARSET', 'utf8');
// define('DB_NAME', 'vanda');
// define('DB_PREFIX', 'vd_');
// define('DB_USER', 'sa');
// define('DB_PASSWORD', 'none');

//	'char_set' => 'utf8',
//	'dbcollat' => 'utf8_general_ci',

define('BACKEND_SPA_MODE', false);
define('BACKEND_SPA_MODE_EXCLUDES', 'Home.indexAction,User.loginAction,User.logoutAction');
define('FRONTEND_SPA_MODE', false);
define('DEV_MODE', true);
