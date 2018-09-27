<?php
namespace System\Image\Libraries;

use System\Image\Base;
use System\File;

class Gd extends Base
{
	public function load($path)
	{
		if (File::exists($path) == false)
		{
			//
			return false;
		}

		static::$_path = $path;
		static::$_mime = static::mime($path);

		if (static::$_mime == false)
		{
			// "Unable to read image from file ({$path})."
			return false;
		}

		switch (static::$_mime)
		{
			case 'image/png':
				static::$_resource = imagecreatefrompng($path);
				static::$_resource = static::_cloneResourceToTrueColor(static::$_resource);
				break;

			case 'image/jpeg':
				static::$_resource = imagecreatefromjpeg($path);
				static::$_resource = static::_cloneResourceToTrueColor(static::$_resource);
				break;

			case 'image/gif':
				static::$_resource = imagecreatefromgif($path);
				static::$_resource = static::_cloneResourceToTrueColor(static::$_resource);
				break;

			default:
				//"Unable to read image type. GD driver is only able to decode JPG, PNG or GIF files."
				return false;
		}

		static::backup();

		return static::$_instance;
	}

	/**
	 * Transform GD resource into Truecolor version
	 * and to preserve transparency when converting
	 * from png -> gif / gif -> png
	 *
	 * @param  resource $resource
	 * @return resource $canvas
	 */
	private function _cloneResourceToTrueColor($resource)
	{
		$width = imagesx($resource);
		$height = imagesy($resource);

		// new canvas
		$canvas = imagecreatetruecolor($width, $height);

		// fill with transparent color
		imagealphablending($canvas, false);
		$transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
		imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
		imagecolortransparent($canvas, $transparent);
		imagealphablending($canvas, true);

		// copy original
		imagecopy($canvas, static::$_resource, 0, 0, 0, 0, $width, $height);

		return $canvas;
	}

	public function backup($name = null)
	{
		$name = trim($name);
		$name = $name ? $name : 'default';

		$canvas = static::_cloneResourceToTrueColor(static::$_resource);
		static::$_backup[$name] = $canvas;

		return static::$_instance;
	}

	public function reset($name = null)
	{
		$name = trim($name);
		$name = $name ? $name : 'default';

		static::$_resource = static::$_backup[$name];

		return static::$_instance;
	}

	public function stream($mime = null, $quality = 90)
	{
		if (is_null($mime))
			$mime = static::$_mime;

		ob_start();

		switch (strtolower($mime))
		{
			case 'image/png':
				// Compression level: 0-9 or -1, where 0 is NO COMPRESSION at all,
				// 1 is FASTEST but produces larger files, 9 provides the best
				// compression (smallest files) but takes a long time to compress,
				// and -1 selects the default compiled into the zlib library.
				// Conclusion: Based on the Zlib manual (http://www.zlib.net/manual.html)
				// the default compression level is set to 6.
				imagepng(static::$_resource, null, -1);
				break;

			case 'image/jpeg':
				$quality = static::_between($quality, 0, 100);
				imagejpeg(static::$_resource, null, $quality);
				break;

			case 'image/gif':
				imagegif(static::$_resource);
				break;

			default:
				//"Unable to read image type. GD driver is only able to decode JPG, PNG or GIF files."
				return false;
		}

		$rawImageBytes = ob_get_clean();

		return $rawImageBytes;
	}

	public function save($path = null, $quality = 90)
	{
		$path = trim($path);
		$path = $path ? $path : static::$_path;

		switch (File::getMimeByExtension($path))
		{
			case 'image/png':
				// Compression level: 0-9 or -1, where 0 is NO COMPRESSION at all,
				// 1 is FASTEST but produces larger files, 9 provides the best
				// compression (smallest files) but takes a long time to compress,
				// and -1 selects the default compiled into the zlib library.
				// Conclusion: Based on the Zlib manual (http://www.zlib.net/manual.html)
				// the default compression level is set to 6.
				imagepng(static::$_resource, $path, -1);
				break;

			case 'image/jpeg':
				$quality = static::_between($quality, 0, 100);
				imagejpeg(static::$_resource, $path, $quality);
				break;

			case 'image/gif':
				imagegif(static::$_resource, $path);
				break;

			default:
				//"Unable to read image type. GD driver is only able to decode JPG, PNG or GIF files."
				return false;
		}

		return static::$_instance;
	}

	public function blur($level = 1)
	{
		$level = (int)$level;

		for ($i = 0; $i < $level; ++$i)
			imagefilter(static::$_resource, IMG_FILTER_GAUSSIAN_BLUR);

		return static::$_instance;
	}

	public function brightness($level)
	{
		$level = static::_between($level, -255, 255);
		imagefilter(static::$_resource, IMG_FILTER_BRIGHTNESS, $level);

		return static::$_instance;
	}

	public function canvas($width, $height, $bgcolor = null)
	{

	}

	public function colorize($red, $green, $blue)
	{
		$red = static::_between($red, -255, 255);
		$green = static::_between($green, -255, 255);
		$blue = static::_between($blue, -255, 255);

		imagefilter(static::$_resource, IMG_FILTER_COLORIZE, $red, $green, $blue);

		return static::$_instance;
	}

	public function contrast($level)
	{
		$level = static::_between($level, -100, 100);
		imagefilter(static::$_resource, IMG_FILTER_CONTRAST, $level);

		return static::$_instance;
	}

	public function crop($width, $height, $x = null, $y = null)
	{
		$sourceWidth = imagesx(static::$_resource);
		$sourceHeight = imagesy(static::$_resource);

		$width = (int)$width;
		$height = (int)$height;

		if ($width == 0 and $height == 0)
			return static::$_instance;

		if ($width > $sourceWidth)
			$width = $sourceWidth;

		if ($height > $sourceHeight)
			$height = $sourceHeight;

		if ($width == $sourceWidth and $height == $sourceHeight)
			return static::$_instance;

		if (is_null($x) and $width < $sourceWidth)
			$x = ($sourceWidth - $width) / 2;

		if (is_null($y) and $height < $sourceHeight)
			$y = ($sourceHeight - $height) / 2;

		$x = (int)$x;
		$y = (int)$y;

		// create new image
		$canvas = imagecreatetruecolor($width, $height);

		// preserve transparency
		$transIndex = imagecolortransparent(static::$_resource);

		if ($transIndex == -1)
		{
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}
		else
		{
			$rgba = imagecolorsforindex($canvas, $transIndex);
			$transColor = imagecolorallocatealpha($canvas, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
			imagefill($canvas, 0, 0, $transColor);
			imagecolortransparent($canvas, $transColor);
		}

		imagecopyresampled($canvas, static::$_resource, 0, 0, $x, $y, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

		static::$_resource = $canvas;

		return static::$_instance;
	}

	public function fit($width, $height = null, $position = 'center')
	{
		$sourceWidth = imagesx(static::$_resource);
		$sourceHeight = imagesy(static::$_resource);

		if (is_null($height))
			$height = $width;

		$width = (int)$width;
		$height = (int)$height;

		$cropRatio = $width / $height;
		$sourceRatio = $sourceWidth / $sourceHeight;

		if ($cropRatio != $sourceRatio)
		{
			if ($cropRatio > $sourceRatio)
			{
				$cropWidth = (int)($sourceHeight * $cropRatio);
				$cropHeight = (int)($sourceHeight / $cropRatio);
			}
			elseif ($cropRatio < $sourceRatio)
			{
				$cropWidth = (int)($sourceHeight * $cropRatio);
				$cropHeight = (int)($sourceHeight / $cropRatio);
			}

			$position = trim($position);
			$x = '';
			$y = '';

			if ($position)
			{
				$position = strtolower($position);

				if (strpos($position, '-'))
				{
					$arr = explode('-', $position);
					$y = $arr[0];

					if (isset($arr[1]))
						$x = $arr[1];
				}
				else
				{
					if (in_array($position, ['top', 'bottom']))
						$y = $position;
					elseif (in_array($position, ['left', 'right']))
						$x = $position;
				}
			}

			switch ($x)
			{
				case 'left':
					$x = 0;
					break;
				case 'right':
					if ($sourceWidth > $cropWidth)
						$x = $sourceWidth - $cropWidth;
					else
						$x = 0;
					break;
				default:
					if ($sourceWidth > $cropWidth)
						$x = ($sourceWidth - $cropWidth) / 2;
					else
						$x = 0;
					break;
			}

			switch ($y)
			{
				case 'top':
					$y = 0;
					break;
				case 'bottom':
					if ($sourceHeight > $cropHeight)
						$y = $sourceHeight - $cropHeight;
					else
						$y = 0;
					break;
				default:
					if ($sourceHeight > $cropHeight)
						$y = ($sourceHeight - $cropHeight) / 2;
					else
						$y = 0;
					break;
			}

			$x = (int)$x;
			$y = (int)$y;

			static::crop($cropWidth, $cropHeight, $x, $y);

			// update dimension of a cropped image
			$sourceWidth = imagesx(static::$_resource);
			$sourceHeight = imagesy(static::$_resource);
		}

		// create new image
		$canvas = imagecreatetruecolor($width, $height);

		// preserve transparency
		$transIndex = imagecolortransparent(static::$_resource);

		if ($transIndex == -1)
		{
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}
		else
		{
			$rgba = imagecolorsforindex($canvas, $transIndex);
			$transColor = imagecolorallocatealpha($canvas, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
			imagefill($canvas, 0, 0, $transColor);
			imagecolortransparent($canvas, $transColor);
		}

		imagecopyresampled($canvas, static::$_resource, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

		static::$_resource = $canvas;

		return static::$_instance;
	}

	public function flipBoth()
	{
		imageflip(static::$_resource, IMG_FLIP_BOTH);
		return static::$_instance;
	}

	public function flipHorizontally()
	{
		imageflip(static::$_resource, IMG_FLIP_HORIZONTAL);
		return static::$_instance;
	}

	public function flipVertically()
	{
		imageflip(static::$_resource, IMG_FLIP_VERTICAL);
		return static::$_instance;
	}

	public function gamma($correction)
	{
		imagegammacorrect(static::$_resource, 1.0, $correction);
		return static::$_instance;
	}

	public function grayscale()
	{
		imagefilter(static::$_resource, IMG_FILTER_GRAYSCALE);
		return static::$_instance;
	}

	public function invert()
	{
		imagefilter(static::$_resource, IMG_FILTER_NEGATE);
		return static::$_instance;
	}

	// If mode is set to boolean true the image will be encoded interlaced. If the parameter is set to false interlaced mode is turned off. Default: true
	public function interlace($interlace = true)
	{
		imageinterlace(static::$_resource, $interlace);

		return static::$_instance;
	}

	public function opacity($transparency = 10)
	{
		$transparency = static::_between($transparency, 0, 100);
//		$color = sprintf('rgba(0, 0, 0, %.1f)', $transparency / 100);
//
//		imagefilter(static::$_resource, IMG_FILTER_NEGATE);
//
//		$image = imagecreatetruecolor(static::width(), static::height());
//		imagesavealpha($image , true);
//		$trans_colour = imagecolorallocatealpha($image , 0, 0, 0, 127);
//		imagefill($image , 0, 0, $trans_colour);

		$alpha = sprintf('%.1f', $transparency / 100);

		$image = imagecreatetruecolor(static::width(), static::height());
		imagecopy($image, static::$_resource, 0, 0, 0, 0, static::width(), static::height());
		imagefill($image, 0, 0, $alpha);

		static::$_resource = $image;

		return static::$_instance;
	}

	public function orientate()
	{
		$orientation = (int)static::exif('Orientation');

		if ($orientation > 1)
		{
			switch ($orientation)
			{
				case 3:
					$angle = 180;
					break;
				case 6:
					$angle = 270;
					break;
				case 8:
					$angle = 90;
					break;
			}

			if ($angle)
			{
				$rotateImage = imagerotate(static::$_resource, $angle, 0);
				static::$_resource = $rotateImage;
			}
		}

		return static::$_instance;
	}

	public function pixelate($size = 10)
	{
		imagefilter(static::$_resource, IMG_FILTER_PIXELATE, $size, true);

		return static::$_instance;
	}

	public function resize($width, $height, $aspectRatio = true)
	{
		$width = (int)$width;
		$height = (int)$height;

		if ($width != 0 or $height != 0)
		{
			$resizeDimension = static::_calResizeDimension($width, $height, $aspectRatio);
			$sourceWidth = $resizeDimension[0];
			$sourceHeight = $resizeDimension[1];
			$width = $resizeDimension[2];
			$height = $resizeDimension[3];

			static::_resize($sourceWidth, $sourceHeight, $width, $height);
		}

		return static::$_instance;
	}

	public function resizeHeight($height)
	{
		$height = (int)$height;

		if ($height != 0)
		{
			$resizeDimension = static::_calResizeDimension(null, $height);
			$sourceWidth = $resizeDimension[0];
			$sourceHeight = $resizeDimension[1];
			$width = $resizeDimension[2];
			$height = $resizeDimension[3];

			static::_resize($sourceWidth, $sourceHeight, $width, $height);
		}

		return static::$_instance;
	}

	public function resizeWidth($width)
	{
		$width = (int)$width;

		if ($width != 0)
		{
			$resizeDimension = static::_calResizeDimension($width, null);
			$sourceWidth = $resizeDimension[0];
			$sourceHeight = $resizeDimension[1];
			$width = $resizeDimension[2];
			$height = $resizeDimension[3];

			static::_resize($sourceWidth, $sourceHeight, $width, $height);
		}

		return static::$_instance;
	}

	private function _calResizeDimension($width, $height, $aspectRatio = true)
	{
		$sourceWidth = imagesx(static::$_resource);
		$sourceHeight = imagesy(static::$_resource);

		$width = (int)$width;
		$height = (int)$height;

		if ($width == 0 and $height == 0)
			return static::$_instance;
		elseif ($width == 0)
			$width = $sourceWidth;
		elseif ($height == 0)
			$height = $sourceHeight;

		if ($aspectRatio)
		{
			$sourceRatio = $sourceWidth / $sourceHeight;

			if ($width / $height > $sourceRatio)
				$width = $height * $sourceRatio;
			else
				$height = $width / $sourceRatio;

			$width = (int)$width;
			$height = (int)$height;
		}

		return [$sourceWidth, $sourceHeight, $width, $height];
	}

	private function _resize($sourceWidth, $sourceHeight, $resizeWidth, $resizeHeight)
	{
		// new canvas
		$canvas = imagecreatetruecolor($resizeWidth, $resizeHeight);

		// fill with transparent color
		imagealphablending($canvas, false);
		$transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
		imagefilledrectangle($canvas, 0, 0, $resizeWidth, $resizeHeight, $transparent);
		imagecolortransparent($canvas, $transparent);
		imagealphablending($canvas, true);

		imagecopyresampled($canvas, static::$_resource, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $sourceWidth, $sourceHeight);

		static::$_resource = $canvas;

		return true;
	}

	public function rotate($angle, $bgcolor = null)
	{
		$rotateImage = imagerotate(static::$_resource, $angle, hexdec($bgColor));
		static::$_resource = $rotateImage;

		return static::$_instance;
	}

	public function pickColor($x, $y, $format = 'rgb')
	{
		$color = imagecolorat(static::$_resource, $x, $y);

		if (imageistruecolor(static::$_resource) == false)
		{
			$color = imagecolorsforindex($image->getCore(), $color);
			$color['alpha'] = round(1 - $color['alpha'] / 127, 2);
		}

		print_r(imagecolorsforindex(static::$_resource, $rgb));exit;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;

		echo $r.'-'.$g.'-'.$b;exit;
	}

	/*
	 * http://zurb.com/forrst/posts/Sharpen_Images_with_PHP_GD-DEr
	 * http://duthler.net/2011/06/23/gd-wrapper-for-php-part-3-sharpen-blur/
	 */
	public function sharpen($level = 10)
	{
		$level = static::_between($level, 0, 100);

		if ($level != 0)
		{
			$min = $level >= 10 ? $level * -0.01 : 0;
			$max = $level * -0.025;
			$abs = ((4 * $min + 4 * $max) * -1) + 1;
			$div = 1;

			$matrix = [
				[$min, $max, $min],
				[$max, $abs, $max],
				[$min, $max, $min]
			];

			imageconvolution(static::$_resource, $matrix, $div, 0);
		}

		return static::$_instance;
	}

	public function destroy()
	{
		@imagedestroy(static::$_resource);
	}

	public function __destruct()
	{
		$this->destroy();
	}
}