<?php
namespace System;

final class Benchmark
{
	private static $_markers = [];

	public static function mark($name)
	{
		$name = trim($name);
		static::$_markers[$name] = microtime(true);

		return true;
	}

	public static function elapsedTime($point1 = null, $point2 = null, $precision = 4)
	{
		if (is_null($point1))
			return '{elapsedTime}';

		$point1 = trim($point1);
		$point2 = trim($point2);

		if (isset(static::$_markers[$point1]) == false)
			return '';

		if (isset(static::$_markers[$point2]) == false)
			static::mark($point2);

		$elapsedTime = number_format(static::$_markers[$point2] - static::$_markers[$point1], $precision);

		return $elapsedTime;
	}

	public function memoryUsage()
	{
		return '{memoryUsage}';
	}
}