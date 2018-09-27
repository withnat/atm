<?php
namespace System;

class Paginator
{
	public static $recordtotal;
	public static $page;
	public static $pagesize;
	public static $pagetotal;
	public static $numstart;
	public static $numend;
	public static $sortcol;
	public static $sortdir;

	private function __construct(){}

	public static function initialize($recordtotal = null, $pagesize = null, $page = null, $sortcol = null, $sortdir = null)
	{
		$context = Uri::getContext();
		
		static::$sortcol = Cookie::get($context . 'sortcol');
		static::$sortdir = Cookie::get($context . 'sortdir');
		static::$page = static::_getPage();
		static::$pagesize = static::_getPageSize();

		static::$pagetotal = ceil(static::$recordtotal / static::$pagesize);
		static::$numstart = ((static::$page - 1) * static::$pagesize) + 1;

		if (static::$page == static::$pagetotal)
			static::$numend = static::$recordtotal;
		elseif (static::$page < static::$pagetotal)
			static::$numend = static::$page * static::$pagesize;
		else
			static::$numend = 1;
	}

	private static function _getPage()
	{
		$context = Uri::getContext();

		if (Request::get('page'))
		{
			$page = Request::get('page');
			Cookie::set($context . 'page', $page);
		}
		elseif (static::$page)
		{
			$page = static::$page;
			Cookie::set($context . 'page', $page);
		}
		else
		{
			$page = Cookie::get($context . 'page');

			if ((int)$page == 0)
			{
				$page = 1;
				Cookie::set($context . 'page', $page);
			}
		}

		return $page;
	}

	private static function _getPageSize()
	{
		$context = Uri::getContext();

		if (static::$pagesize)
		{
			$pagesize = static::$pagesize;
			Cookie::set($context . 'pagesize', $pagesize);
		}
		else
		{
			$pagesize = Cookie::get($context . 'pagesize');

			if ((int)$pagesize == 0)
			{
				$pagesize = \Config::get('pagesize', 20);
				Cookie::set($context . 'pagesize', $pagesize);
			}
		}

		return $pagesize;
	}

	public static function sort($title, $sortcol)
	{
		$html = '<span class="sort" onclick="__vandaSortPage(\'' . $sortcol . '\');">'.$title.'</span>';

		return $html;
	}

	public static function options($options = null)
	{
		if (is_null($options))
			$options = [5, 10, 15, 20, 25, 30, 50, 100, 500, 1000];
		elseif (is_array($options) == false)
		{
			$options = explode(',', $options);
			$options = array_map('trim', $options);
		}

		$attribs = 'class="form-control select paginator-pagesize" onchange="__vandaSetPageSize(this[selectedIndex].value);"';
		$select = Form::select('pagesize', $options, static::$pagesize, null, $attribs);
		$html = t('Show') . $select . t('entries');

		return $html;
	}

	public static function detail()
	{
		$html = t('Showing') . ' ' . number_format(static::$numstart) . ' ';
		$html .= t('to') . ' ' . number_format(static::$numend) . ' ';
		$html .= t('of') . ' ' . number_format(static::$recordtotal) . ' ' . t('entries');

		return $html;
	}

	public static function link($numberOfPages = null)
	{
		if (is_null($numberOfPages))
			$numberOfPages = \Config::get('numberOfPages', 10);

		if (static::$pagetotal)
		{
			$first = '';
			$previous = '';
			$next = '';
			$last = '';

			if (static::$page > 1)
			{
				$first = '<li class="paginate_button"><a onclick="__vandaGoToPage(1);">&lt;&lt;</a></li>';
				$previous = '<li class="paginate_button"><a onclick="__vandaGoToPage(' . (static::$page - 1) . ');">&lt;</a></li>';
			}

			if (static::$pagetotal > static::$page)
			{
				$next = '<li class="paginate_button"><a onclick="__vandaGoToPage(' . (static::$page + 1) . ');">&gt;</a></li>';
				$last = '<li class="paginate_button"><a onclick="__vandaGoToPage(' . static::$pagetotal . ');">&gt;&gt;</a></li>';
			}

			if ($numberOfPages == 'all')
			{
				$min = 1;
				$max = static::$pagetotal;
			}
			else
			{
				// If $numberOfPages is 9
				$backward = (int)($numberOfPages / 2); // This will be 4
				$forward = $numberOfPages - $backward; // This will be 5

				$min = static::$page - $backward;
				$max = static::$page + $forward;

				if ($min < 1)
				{
					$max += abs($min);
					$min = 1;
				}

				if ($max > static::$pagetotal)
					$max = static::$pagetotal;
			}

			$page = '';

			for ($i = $min; $i <= $max; ++$i)
			{
				if ($i == static::$page)
					$page .= '<li class="paginate_button active"><a>' . $i . '</a></li>';
				else
					$page .= '<li class="paginate_button"><a onclick="__vandaGoToPage(' . $i . ');">' . $i . '</a></li>';
			}

			$html = '<ul class="pagination">' . $first . $previous . $page . $next . $last . '</ul>';

			return $html;
		}
	}
}
