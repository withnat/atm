<?php
namespace System;

final class File
{
	private static $_mimes = [
		'hqx'	=>	array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'),
		'cpt'	=>	'application/mac-compactpro',
		'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'),
		'bin'	=>	array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'),
		'dms'	=>	'application/octet-stream',
		'lha'	=>	'application/octet-stream',
		'lzh'	=>	'application/octet-stream',
		'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
		'class'	=>	'application/octet-stream',
		'psd'	=>	array('application/x-photoshop', 'image/vnd.adobe.photoshop'),
		'so'	=>	'application/octet-stream',
		'sea'	=>	'application/octet-stream',
		'dll'	=>	'application/octet-stream',
		'oda'	=>	'application/oda',
		'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
		'ai'	=>	array('application/pdf', 'application/postscript'),
		'eps'	=>	'application/postscript',
		'ps'	=>	'application/postscript',
		'smi'	=>	'application/smil',
		'smil'	=>	'application/smil',
		'mif'	=>	'application/vnd.mif',
		'xls'	=>	array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'),
		'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'),
		'pptx'	=> 	array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'),
		'wbxml'	=>	'application/wbxml',
		'wmlc'	=>	'application/wmlc',
		'dcr'	=>	'application/x-director',
		'dir'	=>	'application/x-director',
		'dxr'	=>	'application/x-director',
		'dvi'	=>	'application/x-dvi',
		'gtar'	=>	'application/x-gtar',
		'gz'	=>	'application/x-gzip',
		'gzip'  =>	'application/x-gzip',
		'php'	=>	array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'),
		'php4'	=>	'application/x-httpd-php',
		'php3'	=>	'application/x-httpd-php',
		'phtml'	=>	'application/x-httpd-php',
		'phps'	=>	'application/x-httpd-php-source',
		'js'	=>	array('application/x-javascript', 'text/plain'),
		'swf'	=>	'application/x-shockwave-flash',
		'sit'	=>	'application/x-stuffit',
		'tar'	=>	'application/x-tar',
		'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
		'z'	=>	'application/x-compress',
		'xhtml'	=>	'application/xhtml+xml',
		'xht'	=>	'application/xhtml+xml',
		'zip'	=>	array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'),
		'rar'	=>	array('application/x-rar', 'application/rar', 'application/x-rar-compressed'),
		'mid'	=>	'audio/midi',
		'midi'	=>	'audio/midi',
		'mpga'	=>	'audio/mpeg',
		'mp2'	=>	'audio/mpeg',
		'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'aif'	=>	array('audio/x-aiff', 'audio/aiff'),
		'aiff'	=>	array('audio/x-aiff', 'audio/aiff'),
		'aifc'	=>	'audio/x-aiff',
		'ram'	=>	'audio/x-pn-realaudio',
		'rm'	=>	'audio/x-pn-realaudio',
		'rpm'	=>	'audio/x-pn-realaudio-plugin',
		'ra'	=>	'audio/x-realaudio',
		'rv'	=>	'video/vnd.rn-realvideo',
		'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
		'bmp'	=>	array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
		'gif'	=>	'image/gif',
		'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
		'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
		'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
		'jp2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'j2k'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpf'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpg2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpx'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpm'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'mj2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'mjp2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'png'	=>	array('image/png',  'image/x-png'),
		'tiff'	=>	'image/tiff',
		'tif'	=>	'image/tiff',
		'css'	=>	array('text/css', 'text/plain'),
		'html'	=>	array('text/html', 'text/plain'),
		'htm'	=>	array('text/html', 'text/plain'),
		'shtml'	=>	array('text/html', 'text/plain'),
		'txt'	=>	'text/plain',
		'text'	=>	'text/plain',
		'log'	=>	array('text/plain', 'text/x-log'),
		'rtx'	=>	'text/richtext',
		'rtf'	=>	'text/rtf',
		'xml'	=>	array('application/xml', 'text/xml', 'text/plain'),
		'xsl'	=>	array('application/xml', 'text/xsl', 'text/xml'),
		'mpeg'	=>	'video/mpeg',
		'mpg'	=>	'video/mpeg',
		'mpe'	=>	'video/mpeg',
		'qt'	=>	'video/quicktime',
		'mov'	=>	'video/quicktime',
		'avi'	=>	array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'),
		'movie'	=>	'video/x-sgi-movie',
		'doc'	=>	array('application/msword', 'application/vnd.ms-office'),
		'docx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'),
		'dot'	=>	array('application/msword', 'application/vnd.ms-office'),
		'dotx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'),
		'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'),
		'word'	=>	array('application/msword', 'application/octet-stream'),
		'xl'	=>	'application/excel',
		'eml'	=>	'message/rfc822',
		'json'  =>	array('application/json', 'text/json'),
		'pem'   =>	array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'),
		'p10'   =>	array('application/x-pkcs10', 'application/pkcs10'),
		'p12'   =>	'application/x-pkcs12',
		'p7a'   =>	'application/x-pkcs7-signature',
		'p7c'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
		'p7m'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
		'p7r'   =>	'application/x-pkcs7-certreqresp',
		'p7s'   =>	'application/pkcs7-signature',
		'crt'   =>	array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'),
		'crl'   =>	array('application/pkix-crl', 'application/pkcs-crl'),
		'der'   =>	'application/x-x509-ca-cert',
		'kdb'   =>	'application/octet-stream',
		'pgp'   =>	'application/pgp',
		'gpg'   =>	'application/gpg-keys',
		'sst'   =>	'application/octet-stream',
		'csr'   =>	'application/octet-stream',
		'rsa'   =>	'application/x-pkcs7',
		'cer'   =>	array('application/pkix-cert', 'application/x-x509-ca-cert'),
		'3g2'   =>	'video/3gpp2',
		'3gp'   =>	array('video/3gp', 'video/3gpp'),
		'mp4'   =>	'video/mp4',
		'm4a'   =>	'audio/x-m4a',
		'f4v'   =>	array('video/mp4', 'video/x-f4v'),
		'flv'	=>	'video/x-flv',
		'webm'	=>	'video/webm',
		'aac'   =>	'audio/x-acc',
		'm4u'   =>	'application/vnd.mpegurl',
		'm3u'   =>	'text/plain',
		'xspf'  =>	'application/xspf+xml',
		'vlc'   =>	'application/videolan',
		'wmv'   =>	array('video/x-ms-wmv', 'video/x-ms-asf'),
		'au'    =>	'audio/x-au',
		'ac3'   =>	'audio/ac3',
		'flac'  =>	'audio/x-flac',
		'ogg'   =>	array('audio/ogg', 'video/ogg', 'application/ogg'),
		'kmz'	=>	array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'),
		'kml'	=>	array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'),
		'ics'	=>	'text/calendar',
		'ical'	=>	'text/calendar',
		'zsh'	=>	'text/x-scriptzsh',
		'7zip'	=>	array('application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
		'cdr'	=>	array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'),
		'wma'	=>	array('audio/x-ms-wma', 'video/x-ms-asf'),
		'jar'	=>	array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'),
		'svg'	=>	array('image/svg+xml', 'application/xml', 'text/xml'),
		'vcf'	=>	'text/x-vcard',
		'srt'	=>	array('text/srt', 'text/plain'),
		'vtt'	=>	array('text/vtt', 'text/plain'),
		'ico'	=>	array('image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon')
	];

	/**
	 * Returns the name without path
	 * @param	string	$file	File path
	 * @return	string			Filename
	 */
	public static function getName($file) // ok
	{
		return basename($file);
	}

	/**
	 * Returns the name without extension
	 * @param	string	$file	File path
	 * @return	string			Filename without extension
	 */
	public static function getNameWithoutExtension($file) // ok
	{
		return basename($file, '.' . static::getExtension($file));
	}

	/**
	 * Gets the extension of a file name
	 *
	 * @param string $file The file name
	 * @return string The file extension
	 */
	public static function getExtension($file) // ok
	{
		return pathinfo($file)['extension'];
	}

	public static function getPath($file) // ok
	{
		return dirname($file);
	}

	public static function changeExtension($file, $newExtension)
	{
		$filename = static::getNameWithoutExtension($file);

		return $filename . '.' . $newExtension;
	}

	//public static function

	/**
	 * Makes file name safe to use
	 *
	 * @param   string  $file  The name of the file [not full path]
	 *
	 * @return  string  The sanitised string
	 */
	public static function makeSafe($file) // ok
	{
		// Remove any trailing dots, as those aren't ever valid file names.
		$file = rtrim($file, '.');
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');

		return trim(preg_replace($regex, '', $file));
	}

	/**
	 * Strips the last extension off a file name
	 *
	 * @param string $file The file name
	 * @return string The file name without the extension
	 */
	public static function stripExtension($file) // ok
	{
		return preg_replace('#\.[^.]*$#', '', $file);
	}

	/**
	 * Returns true if filename exists
	 * @param string $file The file name
	 * @return boolean  True on exists
	 */
	public static function exists($file) // ok
	{
		return is_file($file);
	}

	/**
	 * Delete a file
	 *
	 * @param string $file The file name
	 * @return boolean  True on success
	 */
	public static function delete($file) // ok
	{
		if (static::exists($file))
		{
			@chmod($file, 0755);

			if (@unlink($file))
			{
				$fileName = static::getName($file);
				$resizePath = static::getPath($file) . '/' . RESIZE_DIR;

				if ($fp = @opendir($resizePath))
				{
					while (($entry = readdir($fp)) !== false)
					{
						if ($entry == '.' or $entry == '..')
							continue;

						@unlink($resizePath . '/' . $entry . '/' . $fileName);

						if (Folder::isEmpty($resizePath . '/' . $entry))
							Folder::delete($resizePath . '/' . $entry);
					}

					closedir($fp);

					if (Folder::isEmpty($resizePath))
						Folder::delete($resizePath);
				}
			}
			else
			{
				if  (DEV_MODE)
				{
					$filename = basename($file);
					$error = error_get_last();
					Flash::danger($error['message'].'. Delete failed: '.$filename);
				}

				return false;
			}
		}

		return true;
	}

	public static function isImage($file) // ok
	{
		return @exif_imagetype($file) > 0;
	}

	public static function getSize($file, $precision = 1, $unit = null) // ok
	{
		$size = @filesize($file);
		$size = Number::byteFormat($size, $precision, $unit);

		return $size;
	}

	public static function getAssetPath($filename, $folder, $backtrace)
	{
		$possibleFilePaths = static::_getPossibleAssetPaths($backtrace);

		foreach ($possibleFilePaths as $possibleFilePath)
		{
			if ($possibleFilePath == 'templateAssetFolder')
				$path = TEMPLATEPATH . DS . 'assets' . DS . $folder . DS . $filename;

			elseif ($possibleFilePath == 'templateAssetRootFolder')
				$path = TEMPLATEPATH . DS . 'assets' . DS . $filename;

			elseif ($possibleFilePath == 'packageAssetFolder')
				$path = BASEPATH_PACKAGES . DS . PACKAGE . DS . 'assets' . DS . $folder . DS . $filename;

			elseif ($possibleFilePath == 'packageAssetRootFolder')
				$path = BASEPATH_PACKAGES . DS . PACKAGE . DS . 'assets' . DS . $filename;

			else
				$path = BASEPATH_ASSETS . DS . $folder . DS . $filename;

			if (static::exists($path))
			{
				$path = str_replace(BASEPATH . DS, '', $path);
				return $path;
			}
		}

		return $filename;
	}

	private static function _getPossibleAssetPaths($backtrace)
	{
		if (stripos($backtrace['file'], TEMPLATEPATH) !== false)
			$possibleFilePaths = ['templateAssetFolder', 'templateAssetRootFolder', 'globalAssetFolder'];
		elseif (stripos($backtrace['file'], BASEPATH_PACKAGES . DS . PACKAGE) !== false)
			$possibleFilePaths = ['packageAssetFolder', 'packageAssetRootFolder', 'globalAssetFolder'];
		else
			$possibleFilePaths = ['globalAssetFolder'];

		return $possibleFilePaths;
	}

	public static function getExactFilePath($paths) //refacto แล้ว แต่อาจไม่จำเป็นต้องใช้ รอตรวจสอบตอน refacto ไฟล์ view และระบบ cache
	{
		$paths = (array)$paths;

		foreach ($paths as $path)
		{
			if (static::exists($path))
				return $path;
		}

		return false;
	}

	public static function download($filename, $data = null, $setMime = true) // ok
	{
		$filepath = '';

		if (empty($filename))
			return;
		elseif (is_null($data))
		{
			if (static::exists($filename) == false)
				return;

			$filesize = @filesize($filename);

			if ($filesize === false)
				return;

			$filepath = $filename;
			$filename = static::getName($filename);
		}
		else
			$filesize = strlen($data);

		$mime = 'application/octet-stream';
		$extension = static::getExtension($filename);

		if ($setMime === true)
		{
			if ($extension == '')
				return;

			if (isset(static::$_mimes[$extension]))
			{
				$mime = is_array(static::$_mimes[$extension]) ?
						static::$_mimes[$extension][0] :
						static::$_mimes[$extension];
			}
		}

		$fp = @fopen($filepath, 'rb');

		if (is_null($data) and $fp === false)
			return;

			// Clean output buffer
		if (ob_get_level() !== 0 and @ob_end_clean() === false)
			@ob_clean();

		header('Content-Type: ' . $mime);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $filesize); // show file size during download.
		header('Cache-Control: private, no-transform, no-store, must-revalidate');
		header('Expires: 0');

		if (is_null($data) == false)
			exit($data);

		// Flush 1MB chunks of data
		while (feof($fp) == false and ($data = fread($fp, 1048576)) !== false)
			echo $data;

		fclose($fp);

		exit;
	}

	public static function getMime($file) // ok
	{
		if (File::exists($file))
			return mime_content_type($file);
		else
			return false;
	}

	public static function getMimeByExtension($file) // ok
	{
		$extension = strtolower(static::getExtension($file));

		if (isset(static::$_mimes[$extension]))
		{
			return is_array(static::$_mimes[$extension]) ?
					static::$_mimes[$extension][0] :
					static::$_mimes[$extension];
		}

		return false;
	}

	public static function getInfo($file) // ok
	{
		$fileinfo['name'] = basename($file);
		$fileinfo['path'] = $file;
		$fileinfo['size'] = @filesize($file);
		$fileinfo['date'] = @filemtime($file);
		$fileinfo['readable'] = is_readable($file);
		$fileinfo['writable'] = static::isWritable($file);
		$fileinfo['executable'] = is_executable($file);
		$fileinfo['fileperms'] = @fileperms($file);

		return $fileinfo;
	}

	public static function read($file) // ok
	{
		return @file_get_contents($file);
	}

	public static function write($file, $data, $mode = 'wb') // ok
	{
		@set_time_limit(ini_get('max_execution_time'));

		$fp = @fopen($file, $mode);

		if ($fp == false)
			return false;

		flock($fp, LOCK_EX);

		$result = 0;
		$written = 0;
		$length = strlen($data);

		while ($written < $length)
		{
			$result = fwrite($fp, substr($data, $written));

			if ($result === false)
				break;

			$written += $result;
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}

	/**
	 * Tests for file writability
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link	https://bugs.php.net/bug.php?id=54709
	 * @param	string
	 * @return	bool
	 */
	public static function isWritable($file) // ok
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		//if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
		if (DS === '/') // safe_mode ยกเลิกใน 5.4 ถ้าเราใช้ 5.6+ ก็ไม่ต้องสนบรรทัดบน (รอกลับมาแก้)
			return is_writable($file);
		else
		{
			/* For Windows servers and safe_mode "on" installations we'll actually
			 * write a file then read it. Bah...
			 */
			if (is_dir($file))
			{
				$file = rtrim($file, '/') . '/' . md5(mt_rand());

				if (($fp = @fopen($file, 'ab')) === false)
					return false;

				fclose($fp);
				@chmod($file, 0777);
				@unlink($file);

				return true;
			}
			elseif (is_file($file))
			{
				$fp = @fopen($file, 'ab');

				if ($fp === false)
					return false;

				fclose($fp);

				return true;
			}
			else
				return false;
		}
	}

	public static function getPermission($file) // ok
	{
		$perms = @fileperms($file);

		return substr(sprintf('%o', $perms), -3);
	}

	public static function copy($src, $dest, $overwrite = false) // ok
	{
		if (static::exists($dest) and $overwrite == false)
		{
			// error
			return false;
		}

		return @copy($src, $dest);
	}

	public static function move($src, $dest, $overwrite = false) // ok
	{
		if (static::copy($src, $dest, $overwrite))
			return static::delete($src);
		else
			return false;
	}
}
