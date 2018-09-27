<?php
namespace System;

final class Folder
{
	public static function getDirectorySeparator($path)
	{
		if (strpos($path, '\\'))
			$ds = '\\';
		else
			$ds = '/';

		return $ds;
	}

	public static function create($path, $mode = 0755, $createDefaultHtmlFile = true) // ok
	{
	if (static::exists($path))
			return true;

		if (@mkdir($path, $mode, true))
		{
			$path = str_replace(BASEPATH.'/', '', $path);

			if ($createDefaultHtmlFile)
			{
				$subFolders = explode(DIRECTORY_SEPARATOR, $path);
				$subFolderPath = '';

				foreach ($subFolders as $subFolder)
				{
					$subFolderPath .= $subFolder . '/';
					$file = $subFolderPath . 'index.html';
					$content = '<html><body></body></html>';

					if (File::exists($file) == false)
						File::write($file, $content);
				}
			}

			return true;
		}
		else
		{
			if  (DEV_MODE)
			{
				$error = error_get_last();
				Flash::danger($error['message'].'. Create folder failed: '.$path);
			}

			return false;
		}
	}

	public static function exists($path) // ok
	{
		return $path != '.' and $path != '..' and is_dir($path);
	}

	public static function countItems($path) // ok
	{
		$path = rtrim($path, '/');

		$folders = static::listFolders($path);
		$files = static::listFiles($path);

		$count = count($folders) + count($files);

		foreach ($folders as $folder)
			$count += static::countItems($path . '/' . $folder->name);

		return $count;
	}

	public static function listItems($path) // ok
	{
		$path = rtrim($path, '/');

		if (static::exists($path) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$items = [];

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry == '.' or $entry == '..')
					continue;

				$entryPath = $path . '/' . $entry;

				switch (filetype($entryPath))
				{
					case 'dir':

						$data = new \stdClass;
						$data->type = 'folder';
						$data->name = $entry;
						$data->size = static::countItems($entryPath);
						$data->modified = filemtime($entryPath);

						$items[] = $data;

						break;

					case 'file':

						$data = new \stdClass;
						$data->type = 'file';
						$data->name = $entry;
						$data->size = filesize($entryPath);
						$data->modified = filemtime($entryPath);

						$items[] = $data;

						break;
				}
			}
		}
		else
		{
			// error

			return false;
		}

		return $items;
	}

	public static function listFolders($path) // ok
	{
		$path = rtrim($path, '/');

		if (static::exists($path) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$folders = [];

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				$entryPath = $path . '/' . $entry;

				if ($entry == '.' or $entry == '..' or filetype($entryPath) == 'file')
					continue;

				$data = new \stdClass;
				$data->name = $entry;
				$data->size = static::countItems($entryPath);
				$data->modified = filemtime($entryPath);

				$folders[] = $data;
			}
		}
		else
		{
			// error

			return false;
		}

		return $folders;
	}

	public static function listFiles($path) // ok
	{
		$path = rtrim($path, '/');

		if (static::exists($path) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$files = [];

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				$entryPath = $path . '/' . $entry;

				if ($entry == '.' or $entry == '..' or filetype($entryPath) == 'dir')
					continue;

				$data = new \stdClass;
				$data->name = $entry;
				$data->size = filesize($entryPath);
				$data->modified = filemtime($entryPath);

				$files[] = $data;
			}
		}
		else
		{
			// error

			return false;
		}

		return $files;
	}

	public static function getSize($path) // ok
	{
		$path = rtrim($path, '/');

		if (static::exists($path) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$size = 0;

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry == '.' or $entry == '..')
					continue;

				$entryPath = $path . '/' . $entry;

				$size += filesize($entryPath);

				if (filetype($entryPath) == 'dir')
					$size += static::getSize($entryPath);
			}
		}
		else
		{
			// error

			return false;
		}

		return $size;
	}

	public static function delete($path) // ok
	{
		@set_time_limit(ini_get('max_execution_time'));

		$path = rtrim($path, static::getDirectorySeparator($path));

		if (static::exists($path) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$result = true;

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry == '.' or $entry == '..')
					continue;

				$entryPath = $path . '/' . $entry;

				switch (filetype($entryPath))
				{
					case 'dir':

						$result = static::delete($entryPath);

						break;

					case 'file':

						$result = File::delete($entryPath);

						break;
				}
			}

			closedir($fp);

			if (@rmdir($path) == false)
			{
				if  (DEV_MODE)
				{
					$error = error_get_last();
					Flash::danger($error['message'].'. Delete failed.');
				}

				$result = false;
			}
		}
		else
		{
			// error

			$result = false;
		}

		return $result;
	}

	public static function isEmpty($path) // ok
	{
		$path = rtrim($path, '/');

		if ($fp = @opendir($path))
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry == '.' or $entry == '..')
					continue;

				if (strtolower($entry) == 'index.html')
				{
					$content = File::read($path . '/' . $entry);

					if (trim($content) != '<html><body></body></html>')
						return false;
				}
				else
					return false;
			}

			closedir($fp);
		}
		else
		{
			// error

			return false;
		}

		return true;
	}

	public static function copy($src, $dest, $merge = false, $overwrite = false) // ok
	{
		@set_time_limit(ini_get('max_execution_time'));

		$src = rtrim($src, static::getDirectorySeparator($src));
		$dest = rtrim($dest, static::getDirectorySeparator($dest));

		if (static::exists($src) == false)
		{
			//throw new RuntimeException('Source folder not found', -1);

			return false;
		}

		$result = true;

		if ($fp = @opendir($src))
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry == '.' or $entry == '..')
					continue;

				$srcPath = $src . '/' . $entry;
				$destPath = $dest . '/' . $entry;

				switch (filetype($srcPath))
				{
					case 'dir':

						if (static::exists($destPath) and $merge == false)
						{
							if  (DEV_MODE)
							{
								//throw new RuntimeException('Destination folder already exists', -1);
							}

							$result = false;
						}
						elseif (static::create($destPath) == false)
						{
							if  (DEV_MODE)
							{
								// raise error
							}

							$result = false;
						}
						elseif (static::copy($srcPath, $destPath) == false)
						{
							if  (DEV_MODE)
							{
								// raise error
							}

							$result = false;
						}

						break;

					case 'file':

						if (File::exists($destPath) and $overwrite == false)
						{
							if  (DEV_MODE)
							{
								// raise error
							}

							$result = false;
						}
						elseif (@copy($srcPath, $destPath) == false)
						{
							if  (DEV_MODE)
							{
								$error = error_get_last();
								Flash::danger($error['message'].'. Delete failed.');
							}

							$result = false;
						}

						break;
				}
			}

			closedir($fp);
		}
		else
		{
			// Cannot open source folder : $src
			$result = false;
		}

		return $result;
	}

	public static function move($src, $dest, $merge = false, $overwrite = false) // ok
	{
		if (static::copy($src, $dest, $merge, $overwrite))
			return static::delete($src);
		else
			return false;
	}

	public static function getInfo($path) // ok
	{
		return File::getInfo($path);
	}

	public static function isWritable($path) // ok
	{
		return File::isWritable($path);
	}
}
