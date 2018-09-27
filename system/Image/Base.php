<?php
namespace System\Image;

use System\Str;
use System\Arr;
use System\Folder;
use System\File;
use System\Auth;
use System\Xml;
use System\Csv;

abstract class Base
{
	protected static $_instance;
	protected static $_resource;
	protected static $_path;
	protected static $_width;
	protected static $_height;
	protected static $_filesize;
	protected static $_mime;
	protected static $_backup;

	public function __construct()
	{
		static::$_instance = $this;
	}

	public static function isGIF($file) // ok
	{
		return @exif_imagetype($file) == 1;
	}

	public static function isJPEG($file) // ok
	{
		return @exif_imagetype($file) == 2;
	}

	public static function isPNG($file) // ok
	{
		return @exif_imagetype($file) == 3;
	}

	public static function isSWF($file) // ok
	{
		return @exif_imagetype($file) == 4;
	}

	public static function isPSD($file) // ok
	{
		return @exif_imagetype($file) == 5;
	}

	public static function isBMP($file) // ok
	{
		return @exif_imagetype($file) == 6;
	}

	public static function isTIFF($file) // ok
	{
		$value = @exif_imagetype($file);

		return $value == 7 or $value == 8;
	}

	public static function isJPC($file) // ok
	{
		return @exif_imagetype($file) == 9;
	}

	public static function isJP2($file) // ok
	{
		return @exif_imagetype($file) == 10;
	}

	public static function isJPX($file) // ok
	{
		return @exif_imagetype($file) == 11;
	}

	public static function isJB2($file) // ok
	{
		return @exif_imagetype($file) == 12;
	}

	public static function isSWC($file) // ok
	{
		return @exif_imagetype($file) == 13;
	}

	public static function isIFF($file) // ok
	{
		return @exif_imagetype($file) == 14;
	}

	public static function isWBMP($file) // ok
	{
		return @exif_imagetype($file) == 15;
	}

	public static function isXBM($file) // ok
	{
		return @exif_imagetype($file) == 16;
	}

	public static function isICO($file) // ok
	{
		return @exif_imagetype($file) == 17;
	}

	public static function getLocation($url)
	{
		$backtrace = debug_backtrace();
		$backtrace = $backtrace[0];
		$location = File::getAssetFilePath($url, 'images', $backtrace);

		return $location;
	}

	public static function thumbnail()
	{
	}

	public static function addWatermarkImage($imagePath, $watermarkPath, $watermarkMargin)
	{
	}

	public static function addWatermarkText($imagePath, $watermarkText, $watermarkMargin, $font='arial.ttf', $fontSize=10, $color='black', $bgColor='white')
	{
	}

	/*
	 * Read Exif meta data from current image. Image object must be instantiated from file path to read the EXIF data correctly.
	 * Note: PHP must be compiled in with --enable-exif to use this method. Windows users must also have the mbstring extension enabled.
	 *
	 * key (optional) - Optionally index key to retrieve only particular data. By default all data available will be loaded.
	 * return - Associative array of all Exif data available or mixed data for particular value. If no meta data can be found, method will return NULL.
	 */
	public static function exif($key = null)
	{
		$data = exif_read_data(static::$_path);

		if ($key and is_array($data))
			$data = array_key_exists($key, $data) ? $data[$key] : false;

		return $data;
	}

	public static function mime() // ok
	{
		if (static::$_mime)
			return static::$_mime;
		else
		{
			$value = exif_imagetype(static::$_path);

			if ($value)
				return image_type_to_mime_type($value);
		}

		return false;
	}

	public static function filesize() // ok
	{
		if (static::$_filesize == '')
			static::$_filesize = filesize(static::$_path);

		return static::$_filesize;
	}

	public static function width() // ok
	{
		if (static::$_width == '')
		{
			$size = getimagesize(static::$_path);
			static::$_width = $size[0];
			static::$_height = $size[1];
		}

		return static::$_width;
	}

	public static function height() // ok
	{
		if (static::$_height == '')
		{
			$size = getimagesize(static::$_path);
			static::$_width = $size[0];
			static::$_height = $size[1];
		}

		return static::$_height;
	}

	protected static function _between($level, $min, $max)
	{
		$level = (int)$level;
		$min = (int)$min;
		$max = (int)$max;

		if ($level > $max)
			$level = $max;
		elseif ($level < $min)
			$level = $min;

		return $level;
	}

	public function getResource()
	{
		return static::$_resource;
	}

	abstract public function load($path);
	abstract public function backup($name = null);
	abstract public function reset($name = null);
	abstract public function stream($mime = 'image/jpeg', $quality = 90);
	abstract public function save($path = null, $quality = 90);
	abstract public function destroy();

	abstract public function blur($level = 1);
	abstract public function brightness($level);
	abstract public function canvas($width, $height, $bgcolor = null);
	abstract public function colorize($red, $green, $blue);
	abstract public function contrast($level);
	abstract public function crop($width, $height, $x = null, $y = null);
	abstract public function fit($width, $height = null, $position = 'center');
	abstract public function flipBoth();
	abstract public function flipHorizontally();
	abstract public function flipVertically();
	abstract public function gamma($correction);
	abstract public function grayscale();
	abstract public function invert();
	abstract public function interlace($interlace = true);
	abstract public function opacity($transparency = 10);
	abstract public function orientate();
	abstract public function pickColor($x, $y, $format = 'rgb');
	abstract public function pixelate($size = 10);
	abstract public function resize($width, $height, $aspectRatio = true);
	abstract public function resizeHeight($height);
	abstract public function resizeWidth($width);
	abstract public function rotate($angle, $bgcolor = null);
	abstract public function sharpen($level = 10);
}
