<?php
namespace System;

final class Language
{
	protected static $_strings;

	private function __construct(){}

	private static function _getLanguage($lang)
	{
		$filename = 'languages/' . $lang . '.ini';

		if (File::exists($filename))
			$strings = parse_ini_file($filename);
		else
			$strings = [];

		static::$_strings[$lang] = $strings;
	}

	public static function _($string, $lang = 'default')
	{
		if ($string == '')
			return '';

		if (!isset(static::$_strings[$lang]))
			static::_getLanguage($lang);

		if (isset(static::$_strings[$lang][$string]))
			$string = static::$_strings[$lang][$string];

		return $string;
	}
}
