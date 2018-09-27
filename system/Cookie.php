<?php
namespace System;

final class Cookie
{
	private function __construct(){}

	// Expire default to 0, the cookie will expire at the end of the session (when the browser closes)
	public static function set($name, $value = null, $expire = 0)
	{
		if ($expire)
			$expire += time();

		setcookie($name, $value, $expire, '/');
	}

	public static function get($name, $default = null)
	{
		$value = '';

		if (isset($_COOKIE[$name]))
			$value = $_COOKIE[$name];

		if ($value == '')
			$value = $default;

		return $value;
	}

	public static function clear($name)
	{
		setcookie($name, '', time()-3600, '/');
	}
}
