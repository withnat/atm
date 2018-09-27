<?php
namespace System;

final class FTP
{
	private static $_instance;
	private static $_connection;
	private static $_hostname;
	private static $_username;
	private static $_password;
	private static $_port;

	private function __construct(){}

	public static function connect($hostname, $username, $password, $port = 21, $passive = true)
	{
		if (is_object(static::$_instance) and is_resource(static::$_connection))
			return static::$_instance;

		static::$_connection = @ftp_connect($hostname, $port);

		if (static::$_connection === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		if (@ftp_login(static::$_connection, $username, $password) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		static::$_hostname = $hostname;
		static::$_username = $username;
		static::$_password = $password;
		static::$_port = $port;

		return true;
	}

	public static function createFolder($path, $permission = null)
	{
		if (empty($path) or empty(static::$_connection))
			return false;

		if (@ftp_mkdir(static::$_connection, $path) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		if ($permission)
			static::chmod($path, (int)$permission);

		return true;
	}

	public static function changeFolder($path)
	{
		if (empty($path) or empty(static::$_connection))
			return false;

		if (@ftp_chdir(static::$_connection, $path) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		return true;
	}

	public static function upload($localPath, $remotePath, $mode = 'auto', $permissions = null)
	{
		if (empty($localPath) or empty($remotePath) or empty(static::$_connection))
			return false;

		if (file_exists($localPath) == false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		if ($mode == 'auto')
			$mode = static::getModeByExtension($localPath);

		$mode = ($mode == 'ascii' ? FTP_ASCII : FTP_BINARY);

		if (@ftp_put(static::$_connection, $remotePath, $localPath, $mode) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		if ($permissions)
			static::chmod($path, (int)$permission);

		return true;
	}

	// public static function download_bak($remotePath, $localPath, $mode = 'auto')
	// {
	// 	if (empty($localPath) or empty($remotePath) or empty(static::$_connection))
	// 		return false;
	//
	// 	if ($mode == 'auto')
	// 		$mode = static::getModeByExtension($remotePath);
	//
	// 	$mode = ($mode == 'ascii' ? FTP_ASCII : FTP_BINARY);
	//
	// 	if (@ftp_get(static::$_connection, $localPath, $remotePath, $mode) === false)
	// 	{
	// 		if (DEV_MODE)
	// 		{
	// 			// raise error
	// 		}
	//
	// 		return false;
	// 	}
	//
	// 	return true;
	// }

	public static function getDirectorySeparator($file)
	{
		if (strpos($file, '\\'))
			$ds = '\\';
		else
			$ds = '/';

		return $ds;
	}

	public static function download($remotePath, $localPath, $overwrite = true, $mode = 'auto')
	{
		if (empty($localPath) or empty($remotePath) or empty(static::$_connection))
			return false;

		if (strpos($remotePath, '\\'))
			$remotePathDS = '\\';
		else
			$remotePathDS = '/';

		if (strpos($localPath, '\\'))
			$localPathDS = '\\';
		else
			$localPathDS = '/';

		$ftp = 'ftp://' . static::$_username . ':' . static::$_password
			. '@' . static::$_hostname . ':' . static::$_port . $remotePath;

		if (@is_dir($ftp))
		{
			$files = static::listFiles($remotePath);

			foreach ($files as $file)
			{
				$arrRemotePath = explode($remotePathDS, $remotePath);
				//$localFolder = File::getPath($localPath, $localPathDS);
				//$localPath = $localFolder . '/' . end($arrRemotePath);

				echo 'a '.$file.' - ' . $localPath.'<br />';
				if (static::download($file, $localPath) == false)
					return false;
			}
		}
		else
		{
			if ($mode == 'auto')
				$mode = static::getModeByExtension($remotePath);

			$mode = ($mode == 'ascii' ? FTP_ASCII : FTP_BINARY);

			$localFolder = File::getPath($localPath, $localPathDS);

			if (Folder::isExists($localFolder) == false and Folder::create($localFolder, 0755, false) == false)
			{
				// error

				return false;
			}

			$arrLocalPath = explode($localPathDS, $localPath);
exit;
			if (strpos('.', end($arrLocalPath)) == false)
				$localPath .= '/' . File::getName($remotePath);
			echo 'b '.$remotePath.' - '.$localPath.'<br />';
			if (@ftp_get(static::$_connection, $localPath, $remotePath, $mode) === false)
			{
				if (DEV_MODE)
				{
					// raise error
				}

				return false;
			}
		}

		return true;
	}

	public static function downloadFolder($remotePath, $localPath)
	{
		if (empty($localPath) or empty($remotePath) or empty(static::$_connection))
			return false;

		$localPath = Str::wrapRight($localPath, '/');
		$remotePath = Str::wrapRight($remotePath, '/');

		$files = static::listFiles($remotePath);

		if ($files === false)
			return false;

		foreach ($files as $file)
		{
		//	if (Folder::isExists($localPath . $entry))
		//		static::downloadFolder($remotePath . $entry . '/', $localPath . $entry . '/');
		//	else
		//	{
				$mode = static::getModeByExtension($file);
				static::download($file, $localPath . $file, $mode);
		//	}
		}

		return true;
	}

	public static function rename($oldFile, $newFile)
	{
		if (empty($oldFile) or empty($newFile) or empty(static::$_connection))
			return false;

		if (@ftp_rename(static::$_connection, $oldFile, $newFile) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		return true;
	}

	public static function deleteFile($filePath)
	{
		if (empty($filePath) or empty(static::$_connection))
			return false;

		if (@ftp_delete(static::$_connection, $filePath) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		return true;
	}

	public static function deleteFolder($folderPath)
	{
		if (empty($folderPath) or empty(static::$_connection))
			return false;

		$folderPath = Str::wrapRight($folderPath, '/');

		// ใช้ข้างล่างนี้ของ CI เร็วกว่า 6 เท่า !!!
		// Add a trailing slash to the file path if needed
		//$filepath = preg_replace('/(.+?)\/*$/', '\\1/', $folderPath);

		$files = static::listFiles($folderPath);

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				// If we can't delete the item it's probaly a directory.
				if (preg_match('#/\.\.?$#', $file) == false and @ftp_delete(static::$_connection, $file) == false)
					static::deleteFolder($folderPath.$file);
			}
		}

		if (@ftp_rmdir(static::$_connection, $folderPath) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		return true;
	}

	public static function uploadFolder($localPath, $remotePath)
	{
		if (empty($localPath) or empty($remotePath) or empty(static::$_connection))
			return false;

		$localPath = Str::wrapRight($localPath, '/');
		$remotePath = Str::wrapRight($remotePath, '/');

		$entries = @scandir($localPath);

		if ($entries === false)
			return false;

		if (static::changeFolder($remotePath) == false and
			(static::createFolder($remotePath) == flase or
			static::changeFolder($remotePath) == false))
			return false;

		foreach ($entries as $entry)
		{
			if ($entry != '.' and $entry != '..')
			{
				if (Folder::isExists($localPath . $entry))
					static::uploadFolder($localPath . $entry . '/', $remotePath . $entry . '/');
				else
				{
					$mode = static::getModeByExtension($entry);
					static::upload($localPath . $entry, $remotePath . $entry, $mode);
				}
			}
		}

		return true;
	}



	public static function listFiles($path = '.')
	{
		if (is_resource(static::$_connection))
			return ftp_nlist(static::$_connection, $path);
		else
			return false;
	}

	public static function chmod($path, $permission)
	{
		if (empty($path) or empty(static::$_connection))
			return false;

		if (@ftp_chmod(static::$_connection, $permission, $path) === false)
		{
			if (DEV_MODE)
			{
				// raise error
			}

			return false;
		}

		return true;
	}

	public static function disconnect()
	{
		if (is_resource(static::$_connection))
		{
			static::$_instance = null;
			return @ftp_close(static::$_connection);
		}
		else
			return false;
	}

	public static function getModeByExtension($file)
	{
		$extension = File::getExtension($file);

		if ($extension == '')
			$extension = 'txt';

		$array = ['am', 'asp', 'bat', 'c', 'cfm', 'cgi', 'cnf',
				'conf', 'cpp', 'css', 'ctl', 'dhtml', 'diz',
				'forward', 'grp', 'h', 'hpp', 'htaccess', 'htm',
				'html', 'in', 'inc', 'java', 'js', 'jsp', 'log',
				'lua', 'm4', 'mak', 'map', 'md5', 'nfo', 'nsi',
				'pas', 'patch', 'php', 'php4', 'phps', 'phtml',
				'pl', 'po', 'pwd', 'py', 'qmail', 'sh', 'shtml',
				'sql', 'svg', 'tcl', 'text', 'tpl', 'txt', 'vbs',
				'xhtml', 'xml', 'xrc'];

		if (Arr::has($array, $extension, true))
			return 'ascii';
		else
			return 'binary';
	}
}
