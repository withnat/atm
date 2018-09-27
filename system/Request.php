<?php
namespace System;

final class Request
{
	private function __construct(){}

	public static function set($name, $value, $hash = 'get')
	{
		$hash = trim(strtolower($hash));

		switch ($hash)
		{
			case 'get':
				$_GET[$name] = $value;
				break;

			case 'post':
				$_POST[$name] = $value;
				break;
		}
	}

	public static function get($name=null, $default=null)
	{
		if ($name === null)
			$value = Arr::toObject($_GET);
		else
		{
			$name = trim($name);
			$value = Arr::get($_GET, $name, $default);
		}

		$value = static::_clean($value);

		if (is_array($value))
			$value = Arr::toObject($value);

		return $value;
	}

	public static function post($name = null, $default = null)
	{
		if ($name === null)
			$value = Arr::toObject($_POST);
		else
		{
			$name = trim($name);
			$value = Arr::get($_POST, $name, $default);
		}

		$value = static::_clean($value);

		if (is_array($value))
			$value = Arr::toObject($value);

		return $value;
	}

	public static function keyword()
	{
		
		$key = static::siteContext.'.keyword';
		$context = SIDE.'.'.PACKAGE.'.'.SUBPACKAGE.'.'.ACTION;

		if (isset($_POST['form'][$key]))
		{
			$keyword = $_POST['form'][$key];
			$keyword = static::_clean($keyword);
			Session::set($context.'.'.$key, $keyword);
		}
		else
			$keyword = Session::get($context.'.'.$key);

		return $keyword;
	}

	public static function method()
	{
		return Str::lower(static::server('REQUEST_METHOD'));
	}

	public static function referer()
	{
		return static::server('HTTP_REFERER');
	}

	public static function getClientIp($proxy=false)
	{
		return static::server('REMOTE_ADDR');
	}

	public static function host()
	{
		if (static::isSecure())
			$protocol = 'https://';
		else
			$protocol = 'http://';

		$host = $protocol.static::server('HTTP_HOST');

		return $host;
	}

	public static function baseUrl()
	{
		return static::host().static::basePath();
	}

	public static function homeUrl()
	{
		if (spa())
			$url = '';
		else
			$url = Uri::route();

		return $url;
	}

	public static function basePath()
	{
		return rtrim(dirname(static::server('SCRIPT_NAME')), '/\\');
	}

	public static function uri()
	{
		// IIS not recognizing ‘REQUEST_URI’
		if (strpos(static::server('SERVER_SOFTWARE'), 'IIS') !== false)
			$_SERVER['REQUEST_URI'] = substr(static::server('PHP_SELF'), 0);

		// Remove static::getBasePath() string only first occurrence of a string match.
		// If not, it will remove all matches ie remove 'foo' from /foo/home/foobar.
		return preg_replace('/' . str_replace('/', '\/', static::basePath()) . '/i', '', static::server('REQUEST_URI'), 1);
	}

	public static function url()
	{
		return static::baseUrl().static::uri();
	}

	public static function isGet()
	{
		return (static::method() == 'get');
	}

	public static function isPost()
	{
		return (static::method() == 'post');
	}

	public static function isAjax()
	{
		return ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) == false and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	public static function ensureIsAjax($url = null)
	{
		if (static::isAjax() == false)
		{
			if (trim($url) == '')
				$url = static::homeUrl();

			Response::redirect($url);
		}
	}

	public static function ensureisGet($url = null)
	{
		if (static::isGet() == false)
		{
			if (trim($url) == '')
				$url = static::homeUrl();

			Response::redirect($url);
		}
	}

	public static function ensureIsPost($url = null)
	{
		if (static::isPost() == false)
		{
			if (trim($url) == '')
				$url = static::homeUrl();

			Response::redirect($url);
		}
	}

	public static function isJson()
	{
	}

	public static function isWantsJson()
	{
	}

	public static function isSecure()
	{
		return Arr::has(array('1', 'on'), static::server('HTTPS'), true) or static::server('SERVER_PORT') == 443;
	}

	public static function server($key)
	{
		return Arr::get($_SERVER, $key);
	}

	public static function header($key)
	{
	}

	public static function segment()
	{
	}

	public static function protocol()
	{
		return static::server('SERVER_PROTOCOL');
	}

	/*
	public static function siteContext()
	{
		return md5(static::host());
	}
	*/

	private static function _clean($input)
	{
		/*
		if (static::isMagicQuoteGPC())
			$value = Str::stripSlashes($input);
		*/

		$input = Str::trim($input);
		$input = Str::safe($input);
		$input = Str::removeMultipleSpaces($input);

		return $input;
	}

	public static function boolean($name, $hash = 'post')
	{
		if (static::$hash($name))
			return 1;
		else
			return 0;
	}
}
