<?php
namespace System;

//use System\Arr;
//use System\File;
//use System\Folder;
//use System\Image;
//use System\Message;
//use System\Request;
//use System\Str;
//use System\Uri;

final class Inflector
{
	private function __construct(){}

	public static function pluralize($singular)
	{
	}

	public static function singularize($plural)
	{
		$rules = [ 
			'ss' => false, 
			'os' => 'o', 
			'ies' => 'y', 
			'xes' => 'x', 
			'oes' => 'o', 
			'ies' => 'y', 
			'ves' => 'f', 
			's' => ''
		];

		foreach (array_keys($rules) as $key)
		{
			if (substr($plural, (strlen($key) * -1)) != $key) 
				continue;

			if ($key === false) 
				return $plural;

			return substr($plural, 0, strlen($plural) - strlen($key)) . $rules[$key];
		}

		return $plural;
	}

	public static function camelize($underscored)
	{
	}

	public static function underscore($camelCase)
	{
	}

	public static function humanize($underscored)
	{
	}

	public static function tableize($camelCase)
	{
	}

	public static function classify($underscored)
	{
	}

	public static function variable($underscored)
	{
	}

	public static function slug($word, $replacement = '_')
	{
	}

	public static function reset()
	{
	}
}
