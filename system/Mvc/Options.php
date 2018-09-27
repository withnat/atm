<?php
/*
class Param
{
	public static function get($key)
	{
		$params = Request::get('params');
		$params = explode("\n", $params);

		$value = null;
		foreach ($params as $param)
		{
			$aryparam = explode('=', $param, 2);
			if (trim($aryparam[0]) == trim($key))
			{
				$value = trim($aryparam[1]);
				break;
			}
		}
		return $value;
	}
}
*/

class Option
{
	public static function get($key, $options)
	{
		if   (is_array($options) == false)
			$options = unserialize($options);
		if (isset($options[$key]))
			return $options[$key];
		return false;
	}
}
