<?php
namespace System;

use System\Uri;

final class Response
{
	function toString($compress = false)
	{
		$data = JResponse::getBody();

		// Don't compress something if the server is going todo it anyway. Waste of time.
		if($compress && !ini_get('zlib.output_compression') && ini_get('output_handler')!='ob_gzhandler') {
			$data = JResponse::_compress($data);
		}

		if (JResponse::allowCache() === false)
		{
			JResponse::setHeader( 'Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true ); 				// Expires in the past
			JResponse::setHeader( 'Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true ); 		// Always modified
			JResponse::setHeader( 'Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false );
			JResponse::setHeader( 'Pragma', 'no-cache' ); 											// HTTP 1.0
		}

		JResponse::sendHeaders();
		return $data;
	}

	/**
	* Compress the data
	*
	* Checks the accept encoding of the browser and compresses the data before
	* sending it to the client.
	*
	* @access	public
	* @param	string		data
	* @return	string		compressed data
	*/
	function _compress( $data )
	{
		$encoding = JResponse::_clientEncoding();

		if (!$encoding)
			return $data;

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent())
			return $data;

		if (connection_status() !== 0)
			return $data;


		$level = 4; //ideal level

		/*
		$size		= strlen($data);
		$crc		= crc32($data);

		$gzdata		= "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		$gzdata		.= gzcompress($data, $level);

		$gzdata 	= substr($gzdata, 0, strlen($gzdata) - 4);
		$gzdata 	.= pack("V",$crc) . pack("V", $size);
		*/

		$gzdata = gzencode($data, $level);

		JResponse::setHeader('Content-Encoding', $encoding);
		JResponse::setHeader('X-Content-Encoded-By', 'Joomla! 1.5');

		return $gzdata;
	}

	public static function redirect($url)
	{
		if (spa() and Request::isAjax())
		{
			$data = [
				'title' => '',
				'content' => '',
				'redirect' => Uri::hashSPA($url)
			];

			echo json_encode($data);
			exit;
		}
		else
		{
			header('Location:' . Uri::route($url));
			exit;
		}
	}
}
