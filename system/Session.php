<?php
namespace System;

use System\Session\Handler;

final class Session
{
	protected static $started = false;

	private function __construct(){}

	private static function start()
	{
		if (static::$started == false)
			static::$started = new Handler();
	}

	private static function _getSessionVar()
	{
		return '$_SESSION[\'__vandaSession\']';
	}

	public static function sessionId()
	{
		static::start();

		return session_id();
	}

	public static function set($name, $value)
	{
		static::start();

		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
		{
			if (strpos($arr[$i], '[]'))
			{
				$arr[$i] = str_replace('[]', '', $arr[$i]);
				$sessionVar .= '[$arr[' . $i . ']][]';
			}
			else	
				$sessionVar .= '[$arr[' . $i . ']]';
		}

		if (is_string($value))
			$value = '\'' . $value . '\'';
		elseif (is_bool($value))
			$value = $value ? 'true' : 'false';

		$str = $sessionVar . ' = ' . $value . ';';
		eval($str);
	}

	public static function get($name, $default = null)
	{
		static::start();

		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
			$sessionVar .= '[$arr[' . $i . ']]';

		$value = '';

		$str = 'if (isset(' . $sessionVar . ')) $value = ' . $sessionVar . ';';
		eval($str);

		// Don't return NULL as a default value to SQL REPLACE Statement
		// in Session\Handler. In _write() method, Auth::identity()->id
		// with NULL value will make sql query errors.

		if ($value == '' and is_null($default) == false)
			$value = $default;

		return $value;
	}

	public static function clear($name)
	{
		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
			$sessionVar .= '[$arr[' . $i . ']]';

		eval('unset(' . $sessionVar . ');');
	}

	public static function destroy()
	{
		// Native PHP session_destroy() will 
		// effect after reloading a webpage.
		// So, user still can load webpage twice
		// before they are redirected to login screen.

		$sessionVar = self::_getSessionVar();
		eval('unset(' . $sessionVar . ');');
	}

	public static function getToken()
	{
		$token = static::get('__vandaToken');

		if ($token == '')
		{
			$token = md5(Auth::identity()->id . Str::random(32));
			static::set('__vandaToken', $token);
		}

		return $token;
	}

	public static function checkToken($method = 'post', $url = null)
	{
		return true;
		
		if (Request::$method(static::getToken()) == '')
		{
			Flash::danger(t('Invalid Token'));

			if (trim($url) == '')
				$url = Request::homeUrl();

			Response::redirect($url);
		}

		static::clear('__vandaToken');
	}
}
