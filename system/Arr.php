<?php
namespace System;

use System\Str;

final class Arr
{
	private function __construct(){}

	/**
	 * Return a specific element from an array
	 */
	public static function get($array, $key, $default=null)
	{
		$keys = explode('.', $key);

		foreach ($keys as $key)
		{
			if(is_array($array) == false or array_key_exists($key, $array) == false)
				return $default;

			$array = $array[$key];
		}

		return $array;
	}

	/**
	 * Sets a value to the element at the specified position in an array
	 */
	public static function set($array, $key, $value)
	{
		$key = preg_replace('/[^.a-zA-Z0-9_]+/', '', $key); // prevent string injection
		$keys = Str::replace($key, '.', '\'][\'');

		eval('$array[\''.$keys.'\'] = $value;');

		return $array;
	}

	public static function add($array, $key, $value)
	{
	}

	public static function getKey($array, $key, $value)
	{
	}

	public static function getValue($array, $key, $value)
	{
	}

	public static function first($array)
	{
		$array = array_values($array);
		return array_shift($array);
	}

	public static function last($array)
	{
		return end($array);
	}

	public static function sort()
	{
	}

	/**
	 * Utility function to map an array to a stdClass object.
	 *
	 * @static
	 * @param	array	$array		The array to map.
	 * @param	string	$calss 		Name of the class to create
	 * @return	object	The object mapped from the given array
	 */
	public static function toObject($array, $class='stdClass', $recursive=true, $keys=null)
	{
		$obj = new $class;

		if (is_array($array))
		{
			if (empty($keys) == false and is_array($keys) == false)
				$keys = explode(',', $keys);

			foreach ($array as $key=>$value)
			{
				if (empty($keys) or self::has($keys, $key))
				{
					if (is_array($value))
					{
						if ($recursive)
							$obj->$key = self::toObject($value, $class, $recursive, $keys);
						else
							$obj->$key = new $class;
					}
					else
						$obj->$key = $value;
				}
			}
		}

		return $obj;
	}

	public static function toString($array, $innerGlue='=', $outerGlue=' ', $valueDelimiter='"', $recursive=true, $keys=null)
	{
		$output = array();

		if (is_array($array))
		{
			if (empty($keys) == false and is_array($keys) == false)
				$keys = explode(',', $keys);

			foreach ($array as $key=>$value)
			{
				if (empty($keys) or self::has($keys, $key))
				{
					if (is_array($value))
					{
						if ($recursive)
							$output[] = self::toString($value, $innerGlue, $outerGlue, $valueDelimiter, $recursive, $keys);
					}
					else
						$output[] = $key.$innerGlue.$valueDelimiter.$value.$valueDelimiter;
				}
			}
		}

		return self::implode($output, $outerGlue);
	}

	/**
	 * Parses str as if it were the query string passed via a URL and sets variables in the current scope
	 * @param	string	$str	The input string
	 * @return	array		The array parsed from the given string
	 */
	public static function fromString($str)
	{
		parse_str($str, $array);

		return $array;
	}

	/**
	 * Utility function to map an object or array to an array
	 *
	 * @static
	 * @param	object		The source object or array
	 * @param	boolean		True to recurve through multi-level objects
	 * @param	string/array	An optional field names
	 * @return	array		The array mapped from the given object
	 */
	public static function fromObject($item, $recursive=true, $keys=null)
	{
		if (is_array($item) or is_object($item))
		{
			if (empty($keys) == false and is_array($keys) == false)
				$keys = explode(',', $keys);

			$result = array();

			foreach ($item as $key=>$value)
			{
				if (empty($keys) or self::has($keys, $key))
				{
					if (is_array($value) or is_object($value))
					{
						if ($recursive)
							$result[$key] = self::fromObject($value, $recursive, $keys);
						else
							$result[$key] = array();
					}
					else
						$result[$key] = $value;
				}
			}
		}
		else
			$result = $item;

		return $result;
	}

	/**
	 * Return the values from a single column in the input array (record set)
	 *
	 * @static
	 * @param	array	$array		A multi-dimensional array (record set) from which to pull a column of values
	 * @param	string	$columnKey	The column of values to return
	 * @param	string	$indexKey		The column to use as the index/keys for the returned array
	 * @return	array				 Returns an array of values representing a single column from the input array
	 */
	public static function getColumn($array, $columnKey, $indexKey=null)
	{
		$result = array();

		foreach ((array)$array as $item)
		{
			if (is_array($item) and isset ($item[$columnKey]))
			{
				if ($indexKey)
					$result[$item[$indexKey]] = $item[$columnKey];
				else
					$result[] = $item[$columnKey];
			}
			elseif (is_object($item) and isset ($item->$columnKey))
			{
				if ($indexKey)
					$result[$item->$indexKey] = $item->$columnKey;
				else
					$result[] = $item->$columnKey;
			}
		}

		return $result;
	}

	/**
	 * Utility function to sort an array of objects on a given field
	 *
	 * @static
	 * @param	array	$arr			An array of objects
	 * @param	string	$k			The key to sort on
	 * @param	int		$direction		Direction to sort in [asc = Ascending] [desc = Descending]
	 * @return	array				The sorted array of objects
	 */
	public static function sortObjects($array, $key, $direction='asc')
	{
		if (Str::lower($direction) == 'desc')
			$direction = -1;
		else
			$direction = 1;

		$GLOBALS['GME:System\Arr::sortObjects'] = array('key'=>$key, 'direction'=>$direction);

		usort($array, function($a, $b){
			$params = $GLOBALS['GME:System\Arr::sortObjects'];

			if (strcmp($a->$params['key'], $b->$params['key']) > 0)
				return $params['direction'];
			elseif (strcmp($a->$params['key'], $b->$params['key']) < 0)
				return $params['direction']*-1;
			else
				return 0;
		});

		unset($GLOBALS['GME:System\Arr::sortObjects']);

		return $array;
	}

	public static function has($array, $search, $caseInsensitive=false)
	{
		/*
		in_array() returns true if $search is 0

		Casting any string that doesn't start with a digit to a number results in 0 in PHP. 
		And this is exactly what happens when comparing 0 with some string. 
		See the PHP docs for details about how comparisons between various types are done.

		Use the third argument (set it to true) of in_array to avoid loose type comparison.
		*/
		if ($caseInsensitive)
			return in_array(strtolower($search), array_map('strtolower', $array), true);
		else
			return in_array($search, $array, true);
	}

	public static function implode($pieces, $glue='', $recursive=true)
	{
		$str = '';

		foreach ($pieces as $piece)
		{
			if (is_array($piece))
			{
				if ($recursive)
					$str .= self::implode($piece, $glue).$glue;
			}
			else
				$str .= $piece.$glue;
		}

		$str = Str::trimRight($str, Str::len($glue));

		return $str;
	}

	/**
	 * Flatten a multi-dimensional array (record set) into a single-dimensional array
	 *
	 * @static
	 * @param	array	$array		A multi-dimensional array (record set) from which to pull a column of values
	 * @return	array				Returns an array of values representing a single column from the input array
	 */
	public static function flatten($array)
	{
		$result = array();

		if (is_array($array))
		{
			$item = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));

			foreach($item as $value)
				$result[] = $value;
		}

		return $result;
	}

	/**
	* Method to determine if an array is an associative array
	*
	* @param		array	$array 	An array to test
	* @return		boolean			True if the array is an associative array
	*/
	public static function isAssoc($data)
	{
		return is_array($data) and array_keys($data) !== range(0, count($data)-1);
	}

	public static function removeEmpty($item, $recursive=true)
	{
		$result = array();

		foreach ($item as $itemKey=>$itemValue)
		{
			if ($recursive and is_array($itemValue))
				$result[$itemKey] = self::removeEmpty($itemValue, $recursive);
			elseif (empty($itemValue) == false)
				$result[$itemKey] = $itemValue;
		}

		return $result;
	}

	public static function removeBlank($item, $recursive=true)
	{
		$result = array();

		foreach ($item as $itemKey=>$itemValue)
		{
			if ($recursive and is_array($itemValue))
				$result[$itemKey] = self::removeBlank($itemValue, $recursive);
			elseif (Str::isBlank($itemValue) == false)
				$result[$itemKey] = $itemValue;
		}

		return $result;
	}

	public static function removeKey($item, $key, $recursive = true)
	{
		$result = [];

		foreach ($item as $itemKey => $itemValue)
		{
			if (static::has((array)$key, $itemKey) == false)
			{
				if ($recursive and is_array($itemValue))
					$itemValue = static::removeKey($itemValue, $key, $recursive);

				$result[$itemKey] = $itemValue;
			}
		}

		return $result;
	}

	public static function removeValue($item, $value, $recursive=true)
	{
		$result = array();

		foreach ($item as $itemKey=>$itemValue)
		{
			if ($recursive and is_array($itemValue))
				$result[$itemKey] = self::removeValue($itemValue, $value, $recursive);
			elseif (self::has((array)$value, $itemValue) == false)
				$result[$itemKey] = $itemValue;
		}

		return $result;
	}

	public static function search($array, $search)
	{
		return array_search($search, $array);
	}

	public static function toIndex($item)
	{
		if (is_array($item) or is_object($item))
		{
			$item = (array)$item;
			$item = array_values($item);

			foreach ($item as $key => $value)
				$item[$key] = static::toIndex($value);
		}

		return $item;
	}

	/*
	public static function isMulti($array)
	{
		if (is_array($array) == false)
			return false;

		if (is_array($array[0]) or is_object($array[0]))
			return true;
		else
			return false;
	}
	*/
}
