<?php
namespace System;

class Image
{
	protected static $_library = 'gd'; // gd|imagick

	public static function factory()
	{
		if (static::$_library == 'gd')
			return new Image\Libraries\Gd();
	}

	public static function __callStatic($method, $args)
	{
		if (static::$_library == 'gd')
			$obj = new Image\Libraries\Gd();

		return call_user_func_array(array($obj, $method), $args);
	}
}