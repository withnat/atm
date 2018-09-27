<?php
namespace System;

final class Str
{
	private function __construct(){}

	public static function len($str)//ok
	{
		return mb_strlen($str);
	}

	public static function getLeft($input, $length=1, $recursive=true, $keys=null)//ok
	{
		return self::_get($input, $length, $recursive, $keys, function($value, $length){
			return mb_substr($value, 0, $length);
		});
	}

	public static function getRight($input, $length=1, $recursive=true, $keys=null)//ok
	{
		return self::_get($input, $length, $recursive, $keys, function($value, $length){
			return mb_substr($value, (0-$length));
		});
	}

	public static function sub($input, $start, $length=null, $recursive=true, $keys=null)//ok
	{
		return self::_sub($input, $start, $length, $recursive, $keys, function($value, $start, $length){

			$start = (int)$start;
			$length = (int)$length;

			if ($length == 0)
				return mb_substr($value, $start);
			else
				return mb_substr($value, $start, $length);
		});
	}

	public static function trimLeft($input, $characterMask=null, $recursive=true, $keys=null)//ok
	{
		return self::_trim($input, $characterMask, $recursive, $keys, function($value, $characterMask){

			if ($characterMask === null)
				return ltrim($value);
			else
			{
				if (is_numeric($characterMask))
					$start = $characterMask;
				elseif (self::getLeft($value, self::len($characterMask)) == $characterMask)
					$start = self::len($characterMask);
				else
					$start = 0;

				return mb_substr($value, $start);
			}
		});
	}

	public static function trimRight($input, $characterMask=null, $recursive=true, $keys=null)//ok
	{
		return self::_trim($input, $characterMask, $recursive, $keys, function($value, $characterMask){

			if ($characterMask === null)
				return rtrim($value);
			else
			{
				if (is_numeric($characterMask))
				{
					$length = $characterMask;

					if ($length < 0)
						return mb_substr($value, 0, abs($length));
				}
				elseif (self::getRight($value, self::len($characterMask)) == $characterMask)
					$length = self::len($characterMask);
				else
					$length = 0;

				return mb_substr($value, 0, self::len($value)-$length);
			}
		});
	}

	public static function trim($input, $characterMask=null, $recursive=true, $keys=null)//ok
	{
		$input = self::trimLeft($input, $characterMask, $recursive, $keys);
		$input = self::trimRight($input, $characterMask, $recursive, $keys);

		return $input;
	}

	public static function wrapLeft($input, $characterMask, $recursive=true, $keys=null)
	{
		return self::_wrap($input, $characterMask, $recursive, $keys, function($value, $characterMask){

			if (Str::getLeft($value, Str::len($characterMask)) != $characterMask)
				$value = $characterMask.$value;

			return $value;
		});
	}

	public static function wrapRight($input, $characterMask, $recursive=true, $keys=null)
	{
		return self::_wrap($input, $characterMask, $recursive, $keys, function($value, $characterMask){

			if (Str::getRight($value, Str::len($characterMask)) != $characterMask)
				$value .= $characterMask;

			return $value;
		});
	}

	public static function wrap($input, $characterMask, $recursive=true, $keys=null)
	{
		$input = self::wrapLeft($input, $characterMask, $recursive, $keys);
		$input = self::wrapRight($input, $characterMask, $recursive, $keys);

		// In case $input is empty string, it will return one $characterMask, not wrap.
		if ($input == $characterMask)
			$input .= $characterMask;

		return $input;
	}

	public static function safe($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return htmlspecialchars($value);
		});
	}

	public static function safe2Html($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return htmlspecialchars_decode($value);
		});
	}

	public static function encode($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return base64_encode($value);
		});
	}

	public static function decode($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return base64_decode($value);
		});
	}

	public static function addSlashes($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return addslashes($value);
		});
	}

	public static function stripSlashes($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return stripslashes($value);
		});
	}

	public static function stripTags($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return strip_tags($value);
		});
	}

	public static function removeMultipleSpaces($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return preg_replace('!\s+!', ' ', $value);
		});
	}

	public static function lower($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return mb_strtolower($value);
		});
	}

	public static function upper($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return mb_strtoupper($value);
		});
	}

	public static function lcfirst($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return mb_strtolower(mb_substr($value, 0, 1)).mb_substr($value, 1);
		});
	}

	public static function lcwords($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){

			$words = explode(' ', $value);

			for ($i=0, $n=count($words); $i<$n; ++$i)
				$words[$i] = lcfirst($words[$i]);

			return implode(' ', $words);
		});
	}

	public static function ucfirst($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return mb_strtoupper(mb_substr($value, 0, 1)).mb_substr($value, 1);
		});
	}

	public static function ucwords($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return mb_convert_case($value, MB_CASE_TITLE);
		});
	}

	public static function shuffle($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return self::_mb_str_shuffle($value);
		});
	}

	public static function nl2br($input, $recursive=true, $keys=null)//ok
	{
		return self::_format($input, $recursive, $keys, function($value){
			return nl2br($value);
		});
	}

	public static function replace($input, $search, $replace, $limit=null, $recursive=true, $keys=null)//ok
	{
		return self::_replace($input, $search, $replace, $limit, $recursive, $keys, function($value, $search, $replace, $limit){

			$limit = (int)$limit;

			if ($limit == 0)
				return self::_mb_str_replace($value, $search, $replace);
			else
			{
				for ($i=0; $i<$limit; ++$i)
				{
					$position = mb_strpos($value, $search);

					if ($position === false)
						break;

					$value = self::subReplace($value, $replace, $position, self::len($search));
				}

				return $value;
			}
		});
	}

	public static function ireplace($input, $search, $replace, $limit=null, $recursive=true, $keys=null)//ok
	{
		return self::_replace($input, $search, $replace, $limit, $recursive, $keys, function($value, $search, $replace, $limit){

			$limit = (int)$limit;

			if ($limit == 0)
				return self::_mb_str_replace($value, $search, $replace, true);
			else
			{
				for ($i=0; $i<$limit; ++$i)
				{
					$position = mb_stripos($value, $search);

					if ($position === false)
						break;

					$value = self::subReplace($value, $replace, $position, self::len($search));
				}

				return $value;
			}
		});
	}

	/**
	* Support checking '0' in 'if' statement
	* $input = '0';
	* if ($input)
	* ........do something...
	*/
	public static function blank($str)
	{
		/*
		if (is_string($str) or is_numeric($str) or $str === null or is_bool($str))
		{
		*/
			if (strlen(trim($str)) == 0)
				return true;
			else
				return false;
		/*
		}
		else
		{
			// throw error
		}
		*/
	}

	/**
	 * Unfriendly and easily mistaken characters (i o 0 1 l O) are excluded.
	 * Type:
	 *	alpha: A string with lower and uppercase letters only.
	 *	alnum: Alpha-numeric string with lower and uppercase characters.
	 *	numeric: Numeric string.
	 *	nozero: Numeric string with no zeros.
	 */
	public static function random($length=8, $type='alnum') //ok
	{
		$arrNums = array(0, 1 ,2, 3, 4, 5, 6, 7, 8, 9);
		$arrAlphas = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z',
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z');

		$type = self::lower($type);

		switch ($type)
		{
			case 'numeric':
				$arrChars = $arrNums;
				break;

			case 'alpha':
				$arrChars = $arrAlphas;
				break;

			case 'nozero':
				$arrChars = Arr::removeValue($arrNums, 0);
				break;

			default:
				$arrNums = Arr::removeValue($arrNums, array(0, 1));
				$arrChars = array_merge($arrNums, $arrAlphas);
		}

		while (true)
		{
			$str = '';

			while (self::len($str) < $length)
				$str .= $arrChars[mt_rand(0, count($arrChars)-1)];

			if ($type == 'alnum' and (is_numeric($str) or preg_match('/[^A-Za-z]/', $str) == false))
				$str = self::random($length, $type);

			break;
		}

		return $str;
	}

	private static function _get($input, $length, $recursive, $keys, $callback)//ok
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_get($value, $length, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value, $length);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_get($value, $length, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value, $length);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input, $length);

		return $output;
	}

	private static function _sub($input, $start, $length, $recursive, $keys, $callback)//ok
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_sub($value, $start, $length, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value, $start, $length);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_sub($value, $start, $length, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value, $start, $length);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input, $start, $length);

		return $output;
	}

	private static function _trim($input, $characterMask, $recursive, $keys, $callback)//ok
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		// Prevent to get loaded recursively causing an infinite loop.
		//$keys = Str::trim($keys);

		//for ($i=0, $n=count($keys); $i<$n; ++$i)
		//	$keys[$i] = trim($keys[$i]); // มีไว้ทำอะไร? ลืม...

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_trim($value, $characterMask, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value, $characterMask);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_trim($value, $characterMask, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value, $characterMask);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input, $characterMask);

		return $output;
	}

	private static function _wrap($input, $characterMask, $recursive, $keys, $callback)
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_add($value, $characterMask, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value, $characterMask);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_add($value, $characterMask, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value, $characterMask);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input, $characterMask);

		return $output;
	}

	private static function _format($input, $recursive, $keys, $callback)//ok
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_format($value, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_format($value, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input);

		return $output;
	}

	private static function _replace($input, $search, $replace, $limit, $recursive, $keys, $callback)//ok
	{
		if (empty($keys) == false and is_array($keys) == false)
			$keys = explode(',', $keys);

		if (is_object($input))
		{
			$class = get_class($input);
			$output = new $class;

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output->$key = self::_replace($value, $search, $replace, $limit, $recursive, $keys, $callback);
					else
						$output->$key = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output->$key = $callback($value, $search, $replace, $limit);
				else
					$output->$key = $value;
			}
		}
		elseif (is_array($input))
		{
			$output = array();

			foreach ($input as $key=>$value)
			{
				if ((is_array($value) or is_object($value)))
				{
					if ($recursive)
						$output[$key] = self::_replace($value, $search, $replace, $limit, $recursive, $keys, $callback);
					else
						$output[$key] = $value;
				}
				elseif (empty($keys) or Arr::has($keys, $key))
					$output[$key] = $callback($value, $search, $replace, $limit);
				else
					$output[$key] = $value;
			}
		}
		else
			$output = $callback($input, $search, $replace, $limit);

		return $output;
	}

	public static function subReplace($subject, $replace, $start, $length=null)//ok
	{
		$subjectLength = self::len($subject);

		if ($start < 0)
			$start = max(0, $subjectLength+$start);
		else if ($start > $subjectLength)
			$start = $subjectLength;

		if ($length < 0)
			$length = max(0, $subjectLength-$start+$length);
		else if ($length === null or $length > $subjectLength)
			$length = $subjectLength;

		if (($start+$length) > $subjectLength)
			$length = $subjectLength-$start;

		$block1 = mb_substr($subject, 0, $start).$replace;
		$block2 = mb_substr($subject, $start+$length, $subjectLength-$start-$length);

		return $block1.$block2;
	}

	private static function _mb_str_replace($subject, $search, $replace, $caseInsensitive=false)//ok
	{
		foreach ((array)$search as $key=>$s)
		{
			if (empty($s))
				continue;

			if (is_array($replace))
			{
				if (array_key_exists($key, $replace))
					$r = $replace[$key];
				else
					$r = '';
			}
			else
				$r = $replace;

			if ($caseInsensitive)
				$position = mb_stripos($subject, $s);
			else
				$position = mb_strpos($subject, $s);

			while ($position !== false)
			{
				$subject = mb_substr($subject, 0, $position).$r.mb_substr($subject, $position+self::len($s));

				if ($caseInsensitive)
					$position = mb_stripos($subject, $s, $position+self::len($r));
				else
					$position = mb_strpos($subject, $s, $position+self::len($r));
			}
		}

		return $subject;
	}

	private static function _mb_str_shuffle($str)//ok
	{
		$tmp = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
		shuffle($tmp);

		return implode('', $tmp);
	}

	public static function limit($str, $length, $end='...')
	{
	}

	public static function startWith($str, $startWith)
	{
	}

	public static function endWith($str, $endWith)
	{
	}

	public static function has($str, $endWith)
	{
	}

	public static function finishWith($str, $finishWith)
	{
	}

	public static function slug($str)
	{
	}

	public static function hash($string)
	{
		return password_hash($string, PASSWORD_DEFAULT);
	}
}
