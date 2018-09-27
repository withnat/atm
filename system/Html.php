<?php
namespace System;

final class Html
{
	public static $addedCss = [];
	public static $addedJs = [];
	private static $_printedOutCss = [];
	private static $_printedOutJs = [];

	private function __construct(){}

	public static function toggleid()
	{
		return '<input type="checkbox" id="checkall-toggle" class="i-checks" />';
	}

	public static function checkid($id)
	{
		return '<input type="checkbox" class="i-checks" name="id[]" value="' . $id . '" />';
	}

	public static function status_bak($status)
	{
		switch ($status)
		{
			case -3:
				$text = t('Discontinued');
				break;
			case -2:
				$text = t('Trashed');
				break;
			case -1:
				$text = t('Archived');
				break;
			case 0:
				$text = t('Disabled');
				break;
			case 1:
				$text = t('Enabled');
				break;
			case 2:
				$text = t('Published');
				break;
			default:
				$text = '';
		}

		return $text;
	}

	public static function is($value, $compareValue = 1)
	{
		if ($value == $compareValue)
			$html = '<i class="fa fa-check text-navy"></i>';
		else
			$html = '<i class="fa fa-check text-muted"></i>';

		return $html;
	}

	public static function active($status)
	{
		if ($status > 0)
			$html = '<i class="fa fa-check text-navy"></i>';
		else
			$html = '<i class="fa fa-check text-muted"></i>';

		return $html;
	}

	public static function published($status)
	{
		if ($status == 2)
			$html = '<i class="fa fa-check text-navy"></i>';
		else
			$html = '<i class="fa fa-check text-muted"></i>';

		return $html;
	}

	public static function charset($charset = null)
	{
	}

	public static function link($url = null, $title = null, $attribs = null)
	{
		$routeUrl = Uri::route($url);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::blank($title))
			$title = $routeUrl;

		if (spa())
		{
			$hash = Uri::hashSPA($url);
			$attribs = Html::setAttribute($attribs, 'href', $hash);
			$attribs = Html::setAttribute($attribs, 'data-url', $routeUrl);
		}
		else
			$attribs = Html::setAttribute($attribs, 'href', $routeUrl);

		$html = '<a' . $attribs . '>' . $title . '</a>';
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function linkUnlessCurrent($url = null, $title = null, $attribs = null)
	{
		$currentUrl = Request::url();
		$url = Uri::route($url);

		if (Str::blank($title))
			$title = $url;

		if ($url != $currentUrl)
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			return '<a href="'.$url.'" '.$attribs.'>'.$title.'</a>';
		}
		else
			return $title;
	}

	public static function mailto($email, $title = null, $attribs = null)
	{
		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::blank($title))
			$title = $email;

		return '<a href="mailto:'.$email.'" '.$attribs.'>'.$title.'</a>';
	}

	public static function enable($input, $taskPrefix=null, $id=null)
	{
		if (is_object($input))
		{
			$status = $input->status;
			if ($id === null)
				$id = $input->id;
		}
		else
			$status = $input;

		$enabled = ($status == 1) ? true : false;
		$url = Uri::route(PACKAGE.'/'.(SUBPACKAGE ? SUBPACKAGE.'/' : ''));

		if ($enabled)
		{
			if (isset($id))
				return '<a href="'.$url.$taskPrefix.'disable?id='.$id.'">'.Html::image('tick.png').'</a>';
			else
				return Html::image('tick.png');
		}
		else
		{
			if (isset($id))
				return '<a href="'.$url.$taskPrefix.'enable?id='.$id.'">'.Html::image('x.png').'</a>';
			else
				return Html::image('x.png');
		}
	}

	public static function boonlean($value)
	{
		if ($value)
			return Html::image('tick.png');
		else
			return Html::image('x.png');
	}

	public static function image($url, $alt = null, $attribs = null, $width = null, $height = null)
	{
		return '';
		$backtrace = debug_backtrace();
		$backtrace = $backtrace[0];

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if ($alt)
			$attribs .= ' alt="'.$alt.'" title="'.$alt.'"';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			if (substr($url, 0, 1) != '/')
			{
				if (File::exists($url))
					$path = $url;
				else
					$path = File::getAssetPath($url, 'images', $backtrace);
			}
			else
				$path = Str::stripFirst($url);

			if (($width or $height) and File::exists($path))
			{
				$ori_path = $path;

				$arr = explode('/', $path);
				$filename = $arr[count($arr)-1];
				$arr[count($arr)-1] = '';
				$path = implode('/', $arr);

				if ($width and !$height)
					$height = 'Unlimited';
				elseif (!$width and $height)
					$width = 'Unlimited';
				elseif (!$width and !$height)
				{
					$width = 300;
					$height = 300;
				}

				$resizePath = $path.'resize/'.$width.'x'.$height;
				$path = $resizePath.'/'.$filename;

				if (File::exists($path) == false)
				{
					if (Folder::exists($resizePath) == false)
						Folder::create($resizePath);

					$filetype = strtolower(strrchr($filename, '.'));

					switch ($filetype)
					{
						case '.gif':
							$ori_image = imagecreatefromgif($ori_path);
							break;

						case '.jpg':
						case '.jpeg':
							$ori_image = imagecreatefromjpeg($ori_path);
							break;

						case '.png':
							$ori_image = imagecreatefrompng($ori_path);
							break;
					}

					$orig_width = imagesx($ori_image);
					$orig_height = imagesy($ori_image);

					if ($width == 'Unlimited')
						$width = 10000;
					if ($height == 'Unlimited')
						$height = 10000;

					if ($orig_width > $width or $orig_height > $height)
					{
						$new_width = ($orig_width * $height) / $orig_height;
						$new_height = ($orig_height * $width) / $orig_width;

						if ($new_height > $height)
						{
							$new_height = $height;
							$new_width = ($orig_width * $height) / $orig_height;
						}
						else
						{
							$new_width = $width;
							$new_height = ($orig_height * $width) / $orig_width;
						}
					}
					else
					{
						$new_width = $orig_width;
						$new_height = $orig_height;
					}

					$sm_image = imagecreatetruecolor($new_width, $new_height);
					imagecopyresampled($sm_image, $ori_image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);

					switch ($filetype)
					{
						case '.gif':
							imagegif($sm_image, $resizePath.'/'.$filename);
							break;

						case '.jpg':
						case '.jpeg':
							imagejpeg($sm_image, $resizePath.'/'.$filename, 100);
							break;

						case '.png':
							imagepng($sm_image, $resizePath.'/'.$filename, 0);
							break;
					}

					imagedestroy($ori_image);
					imagedestroy($sm_image);
				}
			}

			if (File::exists($path))
			{
				$dimension = Image::getDimension($path);
				$attribs .= ' width="'.$dimension[0].'" height="'.$dimension[1].'"';
			}

			$url = Request::basePath().'/'.$path;
		}

		$html = '<img src="'.$url.'" '.$attribs.' />';

		return $html;
	}

	private static function _getCssUrl($url)
	{
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			$base = Request::basePath();

			if (substr($url, 0, 1) != '/')
			{
				$arr = explode('?', $url);
				$url = $arr[0];
				$query = isset($arr[1]) ? '?' . $arr[1] : '';

				if (File::exists($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'css', $backtrace[1]);
				}

				$url = $base . DS . $path;
			}
			else
				$url = $base . $url;
		}

		if (DS == '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	public static function css($url, $attribs = null)
	{
		list($url, $query) = static::_getCssUrl($url);

		if (in_array($url, static::$_printedOutCss))
		{
			if (DEV_MODE)
			{
				$backtrace = debug_backtrace();
				$msg = 'The "' . $url . '" file being included multiple times.<br />'
					. '&nbsp;&nbsp;file : '.$backtrace[0]['file'].'<br />'
					. '&nbsp;&nbsp;line : '.number_format($backtrace[0]['line']);
				Flash::warning($msg);
			}

			return '';
		}

		static::$_printedOutCss[] = $url;

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		return '<link rel="stylesheet" type="text/css" href="' . $url . $query . '" ' . $attribs . ' />' . "\n";
	}

	public static function addCss($url, $attribs = null)
	{
		list($url, $query) = static::_getCssUrl($url);

		if (!in_array($url, array_column(static::$addedCss, 'url')))
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			static::$addedCss[] = ['url' => $url, 'query' => $query, 'attribs' => $attribs];
		}
	}

	private static function _getJsUrl($url)
	{
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			$base = Request::basePath();

			if (substr($url, 0, 1) != '/')
			{
				$arr = explode('?', $url);
				$url = $arr[0];
				$query = isset($arr[1]) ? '?' . $arr[1] : '';

				if (File::exists($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'js', $backtrace[1]);
				}

				$url = $base . DS . $path;
			}
			else
				$url = $base . $url;
		}

		if (DS == '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	public static function js($url)
	{
		list($url, $query) = static::_getJsUrl($url);

		if (in_array($url, static::$_printedOutJs))
		{
			if (DEV_MODE)
			{
				$backtrace = debug_backtrace();
				$msg = 'The "' . $url . '" file being included multiple times.<br />'
					. '&nbsp;&nbsp;file : '.$backtrace[0]['file'].'<br />'
					. '&nbsp;&nbsp;line : '.number_format($backtrace[0]['line']);
				Flash::warning($msg);
			}

			return '';
		}

		static::$_printedOutJs[] = $url;

		return '<script type="text/javascript" src="' . $url . $query . '"></script>' . "\n";
	}

	public static function addJs($url)
	{
		list($url, $query) = static::_getJsUrl($url);

		if (!in_array($url, array_column(static::$addedJs, 'url')))
			static::$addedJs[] = ['url' => $url, 'query' => $query];
	}

	public static function linkFile($url)
	{
		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
			$href = Request::baseUrl().'/'.$url;
		else
			$href = $url;

		/*
		if (File::isImage($url))
			$html = '<a href="'.$href.'" target="_blank">'.Html::image($url).'</a>';
		else
		{
		*/
			$ary = explode('/', $url);
			$label = $ary[count($ary)-1];
			$html = '<a href="'.$href.'" target="_blank">'.$label.'</a>';
		//}

		return $html;
	}

	public static function listFooter()
	{
		$html = '<table class="listfooter">
			<tr>
				<td>'.t('Checked').': <span id="selected_span">0</span></td>
				<td class="right">&nbsp;</td>
			</tr>
		</table>
		<script type="text/javascript">check();</script>';

		return $html;
	}

	public static function delete_bak($action='delete', $label='Delete', $icon='edit.png', $warningMessage='Are you sure?')
	{
		$html = '';
		$id = Request::get('id');

		if ($id)
		{
			$url = Uri::route(PACKAGE.(SUBPACKAGE?'/'.SUBPACKAGE:'').'/'.$action);
			$html .= '<p class="delete-box"><a href="'.$url.'?id='.$id.'" class="delete" onclick="return confirm(\''.$warningMessage.'\');">'.$label.'</a></p>';
		}

		 return $html;
	}

	public static function discontinue($action='discontinue', $label='Discontinue', $icon='edit.png')
	{
		$html = '';
		$id = Request::get('id');

		if ($id)
			$html .= '<p class="delete-box"><a href="'.Request::baseUrl().(Config::get()->sef?'':'/index'.EXT).BACKENDPATH.'/'.CONTROLLER.'/'.$action.'?id='.$id.'" class="delete" onclick="return confirm(\'Are you sure?\');">'.$label.'</a></p>';

		 return $html;
	}

	public static function br($multiplier)
	{
		return str_repeat('<br />', $multiplier);
	}

	public static function nbs($multiplier)
	{
		return str_repeat('&nbsp;', $multiplier);
	}

	private static function _list()
	{
	}

	public static function ol($items, $attribs = null)
	{
		/*
		if (is_array($items) == false and is_object($items) == false)
			return '';

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);
		*/
	}

	public static function ul()
	{
	}

	public static function refresh($url, $delay)
	{
	}

//	public static function add($action = null, $label = null)
//	{
//		if (Str::blank($label))
//			$label = t('Add');
//
//		if (Str::blank($action))
//			$action = 'form';
//
//		if (strpos($action, '/') === false)
//		{
//			$package = PACKAGE;
//			$subpackage = SUBPACKAGE ? '/'. SUBPACKAGE : '';
//			$url = Uri::route($package . $subpackage . '/' . $action);
//		}
//		else
//			$url = Uri::route($action);
//
//		$html = '<a href="' . $url . '" class="btn btn-default btn-sm">' . $label . '</a>';
//
//		return $html;
//	}
//
//	public static function enable($action = null, $label = null)
//	{
//		if (Str::blank($label))
//			$label = t('Enable');
//
//		if (Str::blank($action))
//			$action = 'enable';
//
//		return static::_setStatus($action, $label);
//	}
//
//	public static function disable($action = null, $label = null)
//	{
//		if (Str::blank($label))
//			$label = t('Disable');
//
//		if (Str::blank($action))
//			$action = 'disable';
//
//		return static::_setStatus($action, $label);
//	}
//
//	public static function trash($action = null, $label = null)
//	{
//		if (Str::blank($label))
//			$label = t('Trash');
//
//		if (Str::blank($action))
//			$action = 'trash';
//
//		return static::_setStatus($action, $label);
//	}
//
//	public static function delete($action = null, $label = null)
//	{
//		if (Str::blank($label))
//			$label = t('Delete');
//
//		if (Str::blank($action))
//			$action = 'delete';
//
//		$url = static::_routeAction($action);
//
//		$html = '<a class="btn btn-default btn-sm btn-toolbar-toggle hide" onclick="doDelete(\'' . $url . '\');">';
//		$html .= $label;
//		$html .= '</a>';
//
//		return $html;
//	}

//	private static function _routeAction($action)
//	{
//		if (strpos($action, '/') === false)
//		{
//			$package = PACKAGE;
//			$subpackage = SUBPACKAGE ? '/'. SUBPACKAGE : '';
//			$url = Uri::route($package . $subpackage . '/' . $action);
//		}
//		else
//			$url = Uri::route($action);
//
//		return $url;
//	}
//
//	private static function _setStatus($action, $label)
//	{
//		$url = static::_routeAction($action);
//
//		$html = '<a class="btn btn-default btn-sm btn-toolbar-toggle hide" onclick="setStatus(\'' . $url . '\');">';
//		$html .= $label;
//		$html .= '</a>';
//
//		return $html;
//	}

	public static function getAttribute($html, $attribName)
	{
		if (stripos($html, $attribName . '="') !== false)
			$quote = '"';
		elseif (stripos($html, $attribName . '=\'') !== false)
			$quote = '\'';
		else
			return '';

		$seek = $attribName . '=' . $quote;

		$start = stripos($html, $seek) + strlen($seek);
		$end = strpos($html, $quote, $start);
		$value = substr($html, $start, ($end - $start));

		return $value;
	}

	public static function setAttribute($attribs, $defaultAttribName, $defaultAttribValue)
	{
		if (is_array($attribs))
		{
			if (isset($attribs[$defaultAttribName]) == false)
				$attribs[$defaultAttribName] = $defaultAttribValue;

			$attribs = Arr::toString($attribs);
		}
		elseif (stripos($attribs, $defaultAttribName . '=') === false)
			$attribs .= ' ' . $defaultAttribName . '="' . $defaultAttribValue . '" ';
		else
			$attribs = ' ' . $attribs; // maybe overwrite by empty attribute ie 'class=""'

		return $attribs;
	}

	public static function removeMultipleSpacesBetweenHTMLAttributes($html)
	{
		$html = preg_replace('!"\s+!', '" ', $html);
		$html = preg_replace('!"\s+>!', '">', $html);

		return $html;
	}

	public static function addAssetsForFileUpload()
	{
		Html::addCss('templates/backend/vanda/bootstrap-fileinput-master/css/fileinput.min.css');
		Html::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/canvas-to-blob.min.js');
		Html::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/sortable.min.js');
		Html::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/purify.min.js');
		Html::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/fileinput.min.js');
		Html::addJs('templates/backend/vanda/bootstrap-fileinput-master/themes/fa/theme.js');
	}
}
