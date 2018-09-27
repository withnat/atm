<?php
namespace System\DB\Drivers;

use System\DB\Base;

class MySQL extends Base
{
	protected static $_identifierLeft = '`';
	protected static $_identifierRight = '`';

	protected static function _connect()
	{
		$dbhost = DB_HOST;
		$dbport = DB_PORT;
		$dbname = DB_NAME;
		$dbuser = DB_USER;
		$dbpassword = DB_PASSWORD;
		//$dbsocket = DB_SOCKET;

		if (trim($dbhost) == '') $dbhost = 'localhost';
		if (trim($dbuser) == '') $dbuser = 'root';

		try {
			static::$_connection = new \PDO('mysql:host='.$dbhost.'; dbname='.$dbname.';charset=utf8', $dbuser, $dbpassword);
			static::$_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
			static::$_connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	protected static function _reconnect()
	{
		
	}
}
