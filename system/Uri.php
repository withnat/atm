<?php
namespace System;

final class Uri
{
	private function __construct(){}

	public static function route($uri = null, $secure = false)
	{
		$uri = trim($uri);

		if (stripos($uri, 'http://') === false and stripos($uri, 'https://') === false)
		{
			if (substr($uri, 0, 1) != '/')
				$uri = '/' . $uri;

			if ((int)\Config::get('sef'))
				$prefix = '';
			else
				$prefix = '/index' . EXT;

			if (SIDE == 'frontend')
			{
				$lang = LANG ? '/' . LANG : '';
				$uri = Request::baseUrl() . $prefix . $lang . $uri;
			}
			else
			{
				$backendpath = \Config::get('backendpath', '/admin');
				$uri = Request::baseUrl() . $prefix . $backendpath . $uri;
			}

			if ($secure)
				$uri = str_replace('http://', 'https://', $uri);
		}

		return $uri;
	}

	public static function slugify($text)
	{
		$alias = preg_replace('/[^A-Za-z0-9.\/ ]/', '', $text);

		$alias = Str::removeMultipleSpaces($alias);
		$alias = str_replace(' ', '-', $alias);
		$alias = strtolower($alias);
		$alias = trim($alias);

		return $alias;
	}

	public static function hashSPA($url)
	{
		$idPos = strpos($url, '?id=');
		$amPos = strpos($url, '&', $idPos);

		if ($idPos and $amPos === false)
			$url = str_replace('?id=', ':', $url);

		$url = '#' . $url;

		return $url;
	}

	public static function getContext()
	{
		$url = Request::url();

		$arr = explode('?', $url);
		$url = $arr[0];

		$context = preg_replace('/[^a-z0-9]+/i', '', $url);

		return $context;
	}
}
