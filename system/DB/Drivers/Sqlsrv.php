<?php
namespace System\DB\Drivers;

use System\DB\Base;


class Sqlsrv extends Base
{
	protected static $_identifierLeft = '"';
	protected static $_identifierRight = '"';

	protected static function _connect() // ok
	{
		$dbhost = DB_HOST;
		$dbport = DB_PORT;
		$dbname = DB_NAME;
		$dbuser = DB_USER;
		$dbpassword = DB_PASSWORD;
		//$dbsocket = DB_SOCKET;

		if (trim($dbhost) == '') $dbhost = 'localhost';
		if (trim($dbuser) == '') $dbuser = 'root';

		// when you connect to non standart 1433 port
		// for win32 use DSN like 'mssql:host=hostname,port;dbname=database'
		// for FreeTDS and DB-Lib use DSN like  'mssql:host=hostname:port;dbname=database'

		try {
			static::$_connection = new \PDO('dblib:host=192.168.1.118:1433;dbname=vanda', 'sa', 'none');
			static::$_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
			static::$_connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			static::$_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}

/* from laravel
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
*/
