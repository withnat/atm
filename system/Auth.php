<?php
namespace System;

// http://php.net/manual/en/function.password-hash.php
// http://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php
// http://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
// https://gist.github.com/nikic/3707231

final class Auth
{
	protected static $_passthruActions = [];
	protected static $_userdata;

	private function __construct(){}

	// Call this method from controller/preInit
	public static function setPassthru($actions)
	{
		if (is_array($actions) == false)
			$actions = explode(',', $actions);

		$actions = array_map('trim', $actions);

		foreach ($actions as $action)
		{
			if ($action)
				static::$_passthruActions[] = $action;
		}
	}

	public static function getPassthru()
	{
		return static::$_passthruActions;
	}

	public static function identity()
	{
		if (empty(static::$_userdata))
		{
			$data = new \stdClass();
			$data->id = Session::get('__vandaAuthId', 0);
			$data->userGroupId = Session::get('__vandaAuthUserGroupId', 0);
			$data->departmentId = Session::get('__vandaAuthDepartmentId', 0);
			$data->name = Session::get('__vandaAuthName');
			$data->username = Session::get('__vandaAuthUserName');
			$data->languageId = Session::get('__vandaAuthLanguageId');
			//$data->avatar = Session::get('__vandaAuthAvatar');

			static::$_userdata = $data;
		}

		return static::$_userdata;
	}

	public static function genPassword($length = 8)
	{
		return Str::random($length);
	}

	public static function login($username, $password)
	{
		if ($username and $password)
		{
			$user = DB::table('User')->where('username', $username)->where('status', 1)->load();

			if ($user->id)
			{
				if ($user->status == 1 and password_verify($password, $user->password))
				{
					$data = ['visited' => date('Y-m-d H:i:s')];
					DB::table('User')->where($user->id)->update($data);
					Auth::loadProfile2Session($user);

					return true;
				}
			}
		}

		sleep(3);

		return false;
	}

	public static function loadProfile2Session($user)
	{
		// TODO auto load all filed

		Session::set('__vandaAuthId', $user->id);
		Session::set('__vandaAuthUserGroupId', $user->userGroupId);
		Session::set('__vandaAuthDepartmentId', $user->departmentId);
		Session::set('__vandaAuthName', $user->name);
		Session::set('__vandaAuthUserName', $user->username);
		Session::set('__vandaAuthLanguageId', $user->languageId);
		//Session::set('__vandaAuthAvatar', $user->avatar); // TODO base64 is larger to storing in session
	}

	public static function logout()
	{
		Session::destroy();
	}

	public static function loggedin()
	{
		return Auth::identity()->id ? true : false;
	}

	// TODO
	public static function canAccess($data, $redirect=null)
	{
		$canAccess = true;

		if (empty($data->id))
			$canAccess = false;
		else
		{
			if (isset($data->status) and $data->status == -2)
				$canAccess = false;

			if (isset($data->branchId) and UserManager::get('branchId') and UserManager::get('branchId') != $data->branchId)
				$canAccess = false;
		}

		if ($canAccess == false and $redirect)
			Response::redirect($redirect);
		elseif ($canAccess == false and ACTION == 'printout')
			die('Data does not exist.');
		else
			return $canAccess;
	}
}
