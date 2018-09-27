<?php
defined('VD') or exit('Access Denied');

use System\Arr;
use System\Auth;
use System\Autoloader;
use System\Benchmark;
use System\Cache;
use System\Cookie;
use System\CSV;
use System\Data;
use System\DateTime;
use System\DB;
use System\Error;
use System\File;
use System\Flash;
use System\Folder;
use System\Form;
use System\FTP;
use System\JSON;
use System\Hash;
use System\Html;
use System\Image;
use System\Number;
use System\Paginator;
use System\Request;
use System\Response;
use System\Session;
use System\Str;
use System\Struct;
use System\Toolbar;
use System\Uri;
use System\XML;

$timestart = microtime(true);

/* Check configuration file */

if (is_file('config.php') == false or (filesize('config.php') < 10))
{
	if (is_file('install/index.php'))
	{
		if (is_writable('.') == false)
			die('Vanda root directory needs to be temporarily writable
				so we can create your application and database configuration files.');

		header('Location: install/index.php');
	}
	else
		echo 'No configuration file found and no installation code available, exiting...';

	exit();
}

/* Include startup files */

include 'config.php';
include BASEPATH_SYSTEM . '/Autoloader.php';
include BASEPATH_SYSTEM . '/Common.php';

/* Check system requirements */
$phpVerMsg = '';
$phpExtMsg = '';
$dbVerMsg = '';
$stopSystem = false;

if (version_compare(PHP_VERSION, '7.0') < 0)
{
	$phpVerMsg = '<li>PHP 7.0 +, you are running ' . PHP_VERSION . '</li>';
	$stopSystem = true;
}

/*
if (extension_loaded('gd') == false)
{
	$phpExtMsg .= '<li>gd</li>';
	$stopSystem = true;
}
*/


if (extension_loaded('mbstring') == false)
{
	$phpExtMsg .= '<li>mbstring</li>';
	$stopSystem = true;
}

if (extension_loaded('pdo_' . strtolower(DB_DRIVER)) == false)
{
	$phpExtMsg .= '<li>pdo_' . strtolower(DB_DRIVER) . '</li>';
	$stopSystem = true;
}

if ($stopSystem)
{
	echo 'Vanda has a few system requirements:';
	echo '<ul>';

	if ($phpVerMsg or $phpExtMsg)
		echo $phpVerMsg;

	if ($phpExtMsg)
		echo '<ul>' . $phpExtMsg . '</ul>';

	echo $dbVerMsg;

	echo '</ul>';

	exit;
}

/* Check path permission */

if (is_writable(BASEPATH_ASSETS . '/') == false)
	die('Asset directory /' . BASEPATH_ASSETS . ' is not writeable by the webserver.');

if (is_writable(BASEPATH_TMP . '/') == false)
	die('Temp directory /' . BASEPATH_TMP . ' is not writeable by the webserver.');

/* Create the Composer autoloader */

$loader = require BASEPATH_SYSTEM . '/vendor/autoload.php';
$loader->unregister();

// Decorate Composer autoloader
spl_autoload_register(array(new System\Autoloader($loader), 'loadClass'), true, true);

/* PHP config */

mb_internal_encoding('UTF-8');

ini_set('arg_separator.output', '&amp;');
ini_set('auto_detect_line_endings', 1);
ini_set('magic_quotes_runtime', 0);
ini_set('session.cache_expire', 1440); //Config::get('lifetime', 30)

// Send an empty string to disable the cache limiter.
ini_set('session.cache_limiter', '');

ini_set('session.cookie_lifetime', 86400); //(Config::get('lifetime', 30) * 60)
ini_set('session.gc_maxlifetime', 86400); //(Config::get('lifetime', 30) * 60)

// Debian/Ubuntu distro, by default PHP disables its session garbage collection mechanism
// (eg. the default php.ini contains the line ;session.gc_probability = 0 in Ubuntu).
// For every page request, there would be a 0.01% chance the Garbage collection method would be run.
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);

ini_set('session.use_cookies', 1);

// It is also a good idea to make sure that PHP only uses cookies for
// sessions and disallow session ID passing as a GET parameter.
ini_set('session.use_only_cookies', 1);

// This is supposed to make session fixation attacks harder by not
// allowing an attacker to make up their own session IDs.
ini_set('session.use_strict_mode', 1); // Available since PHP 5.5.2

// To prevent session hijacking through cross site scripting (XSS)
// you should always filter and escape all user supplied values before
// printing them to screen. However some bugs may slip through or a
// piece of legacy code might be vulnerable so it makes sense to also
// make use of browser protections against XSS.
//
// By specifying the HttpOnly flag when setting the session cookie you
// can tell a users browser not to expose the cookie to client side
// scripting such as JavaScript. This makes it harder for an attacker
// to hijack the session ID and masquerade as the effected user.
ini_set('session.cookie_httponly', 1); // Available since PHP 5.2.0

// Another important way to increase the security of PHP sessions in
// your application is to install an SSL certificate on the web server
// and force all user interactions to occur over HTTPS only. This will
// prevent the users session ID from being transmitted in plain text
// to make it much harder to hijack the users session.
//if (isHttps)
//	ini_set('session.cookie_secure', 1);

				//ini_set('session.hash_function', 1);
				//ini_set('session.hash_bits_per_character', 4);
				//ini_set('url_rewriter.tags', '');

// Log
ini_set('log_errors', 1);
ini_set('error_log', BASEPATH_TMP . '/logs/error.log');

// Time zone
date_default_timezone_set(Config::get('timezone', 'UTC'));

// Display errors
if (DEV_MODE)
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	ini_set('error_reporting', E_ALL);
}
else
{
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	ini_set('error_reporting', 0);
}

// Both options mentioned by others (session.gc_maxlifetime and session.cookie_lifetime)
// are not reliable. This link explains the reasons for that.
// http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
$vandaLastActivityTime = Session::get('__vandaLastActivityTime');
$currentTime = time();

if ($vandaLastActivityTime and $currentTime - $vandaLastActivityTime > Config::get('lifetime', 30) * 60)
	Session::destroy();
else
	Session::set('__vandaLastActivityTime', $currentTime);

/* URL routing */

$uri = $_SERVER['PHP_SELF'];
$baseUri = $_SERVER['SCRIPT_NAME'];

$backendpath = Config::get('backendpath', '/admin');

if (strpos($uri, $backendpath))
{
	$baseUri .= $backendpath;
	define('SIDE', 'backend');
}
else
	define('SIDE', 'frontend');

//

$template = Config::get(strtolower(SIDE).'template', 'vanda');
$templatePath = BASEPATH_TEMPLATES . DS . SIDE . DS . $template;

define('TEMPLATEPATH', $templatePath);
define('TEMPLATENAME', $template);
define('BACKENPATH', $backendpath);

//

$uri = str_replace($baseUri, '', $uri); // remove /vanda/index.php from uri
$uri = trim($uri, '/'); // ie /about/

/* Language */

if (SIDE == 'frontend')
{
	$langDefault = '';
	$langCurrent = '';
	$langCurrentId = '';

	$langs = DB::table('Language')->where('status', 1)->sortDesc('default')->loadAll();
	$arr = explode('/', $uri);

	foreach ($langs as $lang)
	{
		if ($lang->{'default'})
		{
			$langDefault = $lang->sef;
			$langDefaultId = $lang->id;
		}

		// http://localhost/en/about
		if ($arr[0] == $lang->sef)
		{
			$langCurrent = $lang->sef;
			$langCurrentId = $lang->id;

			break;
		}
	}

	// http://localhost/about
	if ($langCurrentId == '')
		$langCurrentId = $langDefaultId;

	if ($uri == '' and $langCurrent != $langDefault)
	{
		if ((int)\Config::get('sef'))
			$prefix = '';
		else
			$prefix = '/index.php';

		$url = Request::baseUrl() . $prefix . '/' . $langDefault;
		Response::redirect($url);
	}

	if ($langCurrent)
		$uri = substr($uri, strlen($langCurrent) + 1); // +1 for slash ie 'en/'

	define('LANG', $langCurrent);
	define('LANG_ID', $langCurrentId);
}
else
{
	define('LANG', '');
	define('LANG_ID', '');
}

/* Package */

$package = '';
$subpackage = '';
$action = '';

if (SIDE == 'frontend')
{
	DB::table('Page')->where('status', 1);

	if ($uri)
	{
		$arr = explode('/', $uri);
		DB::where('alias', $arr[0]);
	}
	else
		DB::where('default', 1);

	$page = DB::load();

	if (is_object($page))
	{
		if ($page->package)
		{
			$package = $page->package;
			$subpackage = $page->subpackage;
			$action = ($page->action ? $page->action : 'indexAction');
		}
		else
		{
			$package = 'home';
			$subpackage = '';
			$action = 'indexAction';

			$_GET['id'] = $page->id;
		}
	}
}

if ($package == '')
{
	if ($uri)
	{
		$segs = explode('/', $uri);
		$package = isset($segs[0]) ? $segs[0] : 'home';

		if (isset($segs[2]))
		{
			$subpackage = $segs[1];
			$action = $segs[2].'Action';
		}
		elseif (isset($segs[1]))
		{
			if (Folder::exists(BASEPATH_PACKAGES.'/'.$package.'/'.SIDE.'/'.$segs[1]))
			{
				$subpackage = $segs[1];
				$action = 'indexAction';
			}
			else
				$action = $segs[1].'Action';
		}
		else
			$action = 'indexAction';
	}
	else
	{
		$package = 'home';
		$subpackage = '';
		$action = 'indexAction';
	}
}

$action = str_replace('_', '-', $action);
$arr = explode('-', $action);

for ($i=1, $n = count($arr); $i < $n; ++$i)
	$arr[$i] = ucfirst($arr[$i]);

$action = implode('', $arr);

/* Run */

Autoloader::importPackage($package, $subpackage);

$controller = ucfirst($package);
$controller .= ($subpackage ? ucfirst($subpackage) : '');
$controller .= 'Controller';

$controller = new $controller();
$controller->preInit();

if (SIDE == 'backend')
{
	$passthruActions = Auth::getPassthru();

	if (Auth::identity()->id == '' and $action != 'loginAction' and Arr::has($passthruActions, $action, 1) == false)
	{
		$redirect = '';

		if (Request::isGet())
		{
			if (Request::get('redirect'))
				$url = Uri::route(Request::get('redirect'));
			else
				$url = Request::url();

			$redirect = '?redirect=' . Str::encode($url);
		}

		Response::redirect('user/login' . $redirect);
	}
}

define('PACKAGE', $package);
define('SUBPACKAGE', $subpackage);
define('ACTION', $action);

$controller->init();
$controller->$action();
$controller->end();

// Response::redirect(Request::homeUrl());

if (DEV_MODE)
{
	$timeend = microtime(true);
	$totaltime = $timeend-$timestart;

//	echo '<p style="color:black; font-size:12px; font-family:tahoma; clear:both;">
//		Memory used: '.number_format((memory_get_usage()/1024), 2).' KB<br />
//		Parsing time: ' .number_format($totaltime, 3). ' seconds.<br />
//		Query: '.count($_query).'<ol>';
//		foreach ($_query as $query)
//			echo '<li>'.$query.'</li>';
//		echo '</ol></p>';
}

$timeend = microtime(true);
$totaltime = $timeend-$timestart;
//echo '<p>Parsing time: ' .number_format($totaltime, 3). ' seconds.<br />';
//echo 'Memory used: '.number_format((memory_get_usage()/1024), 2).' KB</p>';
