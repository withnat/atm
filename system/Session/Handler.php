<?php
namespace System\Session;

use System\Auth;
use System\DB;

class Handler
{
	public function __construct()
	{
		// Cannot setup session.use_trans_sid after session start. Fail on deleting
		// temp file in DEV_MODE will start session via Flash::danger() method.
		// Setting up session.use_trans_sid after that will display warning message.
		// Warning: ini_set(): A session is active. You cannot change ...

		// When use_trans_sid is enabled, PHP will pass the session ID via the
		// URL. This makes it far easier for a malicious party to obtain an
		// active session ID and hijack the session. Defaults to 0.
		// (Only when cookies are not working. Plus, if both session.use_trans_sid
		// and session.use_cookies are 1, then session.use_only_cookies decides: 1
		// will disable URL-rewriting.)
		//ini_set('session.use_trans_sid', 0);

		session_set_save_handler
		(
			['System\Session\Handler', '_open'],
			['System\Session\Handler', '_close'],
			['System\Session\Handler', '_read'],
			['System\Session\Handler', '_write'],
			['System\Session\Handler', '_destroy'],
			['System\Session\Handler', '_gc']
		);

		session_write_close();
		session_start();
	}

	public static function _open()
	{
		return true;
	}

	public static function _close()
	{
		return true;
	}

	public static function _read($id)
	{
		return DB::select('data')
				->from('Session')
				->where('id', $id)
				->loadSingle();
	}

	public static function _write($id, $data)
	{
		DB::raw('REPLACE INTO #_Session VALUES  (' .
				DB::escape($id) . ', ' .
				time() . ', ' .
				DB::escape($data) . ', ' .
				Auth::identity()->id . ')')
				->execute();

		return true;
	}

	public static function _destroy($id)
	{
		if (DB::table('Session')->where('id', $id)->delete())
			return true;
		else
			return false;
	}

	public static function _gc($max)
	{
		$old = time() - $max;

		if (DB::table('Session')->where('access', '<', $old)->delete())
			return true;
		else
			return false;
	}
}
