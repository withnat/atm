<?php
namespace System;

final class Toolbar
{
	public static function add($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = '<i class="fa fa-plus"></i> ' . t('Add');

		if (trim($action) == '')
			$action = 'form';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-add');

		return static::link($action, $label, $attribs);
	}

	public static function activate($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = '<i class="fa fa-check"></i> ' . t('Activate');

		if (trim($action) == '')
			$action = 'activate';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-activate');

		$url = Uri::route(static::_getUri($action));
		$attribs = Html::setAttribute($attribs, 'onclick', '__vandaDoAction(\'' . $url . '\');"');

		return static::button($label, $attribs);
	}

	public static function deactivate($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = '<i class="fa fa-times"></i> ' . t('Deactivate');

		if (trim($action) == '')
			$action = 'deactivate';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-deactivate');

		$url = Uri::route(static::_getUri($action));
		$attribs = Html::setAttribute($attribs, 'onclick', '__vandaDoAction(\'' . $url . '\');"');

		return static::button($label, $attribs);
	}

	public static function archive($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Archive');

		if (trim($action) == '')
			$action = 'archive';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-archive');

		$url = Uri::route(static::_getUri($action));
		$attribs = Html::setAttribute($attribs, 'onclick', '__vandaDoAction(\'' . $url . '\');"');

		return static::button($label, $attribs);
	}

	public static function trash($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Trash');

		if (trim($action) == '')
			$action = 'trash';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-trash');

		$url = Uri::route(static::_getUri($action));
		$attribs = Html::setAttribute($attribs, 'onclick', '__vandaDoAction(\'' . $url . '\');"');

		return static::button($label, $attribs);
	}

	public static function delete($action = null, $label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = '<i class="fa fa-trash"></i> ' . t('Delete');

		if (trim($action) == '')
			$action = 'delete';

		if (is_null($attribs))
			$attribs = Html::setAttribute($attribs, 'id', 'btn-delete');

		$url = Uri::route(static::_getUri($action));
		$onclick = 'bootbox.confirm(\'' . t('Are you sure?') . '\', function(result){
						if (result){
							__vandaDoAction(\'' . $url . '\');
						}
					});';
		$attribs = Html::setAttribute($attribs, 'onclick', $onclick);

		return static::button($label, $attribs);
	}

	public static function link($action, $label, $attribs = null)
	{
		$uri = static::_getUri($action);
		$url = Uri::route($uri);

		if (spa())
		{
			$hash = Uri::hashSPA($uri);
			$attribs = Html::setAttribute($attribs, 'href', $hash);
			$attribs = Html::setAttribute($attribs, 'data-url', $url);
		}
		else
			$attribs = Html::setAttribute($attribs, 'href', $url);

		$attribs = Html::setAttribute($attribs, 'class', 'btn btn-default btn-sm');

		$html = '<a' . $attribs . '>' . $label . '</a>';
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	private static function button($label, $attribs)
	{
		$attribs = Html::setAttribute($attribs, 'class', 'btn btn-default btn-sm btn-toolbar-toggle hide');
		$html = '<a' . $attribs . '>' . $label . '</a>';
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	private static function _getUri($action)
	{
		if (strpos($action, '/') === false)
		{
			$package = PACKAGE;
			$subpackage = SUBPACKAGE ? '/'. SUBPACKAGE : '';
			$uri = $package . $subpackage . '/' . $action;
		}
		else
			$uri = $action;

		return $uri;
	}

	public static function separate()
	{
		return '<span class="separate"></span>';
	}
}
