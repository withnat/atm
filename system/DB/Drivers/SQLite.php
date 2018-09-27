<?php
namespace System\DB\Drivers;

use System\DB\Base;


class SQLite extends Base
{
	protected static $_identifierLeft = '"';
	protected static $_identifierRight = '"';

	protected static function _connect() // ok
	{
		try {
			static::$_connection = new \PDO('sqlite:../vand.sqlite');
			static::$_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
			static::$_connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			static::$_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}
