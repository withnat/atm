<?php
namespace System;

class DB
{

	public function __construct()
	{
		if (DB_DRIVER == 'mysql')
			return new DB\Drivers\MySQL();
	}

	public static function __callStatic($method, $args)
	{
		if (DB_DRIVER == 'mysql')
			$obj = new DB\Drivers\MySQL();

		return call_user_func_array(array($obj, $method), $args);
	}
}


// if (DB_DRIVER == 'mysql')
// {
// 	class DB extends DB\Drivers\MySQL{}
// }
// elseif (DB_DRIVER == 'sqlite')
// {
// 	class DB extends DB\Drivers\SQLite{}
// }
// elseif (DB_DRIVER == 'sqlsrv')
// {
// 	class DB extends DB\Drivers\Sqlsrv{}
// }
