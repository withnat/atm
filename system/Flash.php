<?php
namespace System;

//use System\Session;

final class Flash
{
	private function __construct(){}
 
 	public static function info($message)
 	{
 		Session::set('__vandaFlashInfo[]', $message);
 	}
 
  	public static function success($message)
 	{
	    if (Request::isAjax())
	    {
		    $html = '<div class="alert alert-success alert-dismissable">';
			$html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
			$html .= $message . '</div>';

		    return $html;
	    }
	    else
		    Session::set('__vandaFlashSuccess[]', $message);
 	}
 
  	public static function warning($message)
 	{
 		Session::set('__vandaFlashWarning[]', $message);
 	}
 
   	public static function danger($message)
 	{
	    if (Request::isAjax())
	    {
		    $html = '<div class="alert alert-danger alert-dismissable">';
		    $html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
		    $html .= $message . '</div>';

		    return $html;
	    }
	    else
		    Session::set('__vandaFlashDanger[]', $message);
 	}

//	public static function getInfo()
//	{
//		if (is_array(Session::get('__vandaFlashInfo')))
//		{
//			$tagInfo = str_replace('{{type}}', 'info', $tag);
//
//			$flashInfo .= $tagInfo;
//			$flashInfo .= implode('</div>' . $tagInfo, Session::get('__vandaFlashInfo'));
//			$flashInfo .= '</div>';
//			Session::clear('__vandaFlashInfo');
//		}
//	}

	public static function clear()
	{
		Session::clear('__vandaFlashInfo');
		Session::clear('__vandaFlashSuccess');
		Session::clear('__vandaFlashWarning');
		Session::clear('__vandaFlashDanger');
	}
}
