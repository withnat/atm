<?php
namespace System\Mvc;

use System\Cache;
use System\DB;
use System\File;
use System\Html;
use System\Request;
use System\Session;
use System\Uri;

class View
{
	private $_generator = 'Vanda';
	private $_template = TEMPLATEPATH;
	private $_layout = 'index';
	private $_title;
	private $_pagetitle;
	private $_metakey;
	private $_metadesc;
	private $_metadata = [];
	private $_breadcrumbs = [];
	public static $formVals;

	public function __construct()
	{
	}

	public function getGenerator()
	{
		return $this->_generator;
	}

	public function setGenerator($content)
	{
		$this->_generator = $content;
	}

	public function getTemplate()
	{
		return $this->_template;
	}

	public function setTemplate($template)
	{
		$this->_template = BASEPATH_TEMPLATES . '/' . SIDE . '/' . $template;
	}

	public function getLayout()
	{
		return $this->_layout;
	}

	public function setLayout($layout)
	{
		$this->_layout = $layout;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setTitle($title)
	{
		$this->_title = $title;
	}

	public function getPageTitle()
	{
		return $this->_pagetitle;
	}

	public function setPageTitle($title)
	{
		$this->_pagetitle = $title;
	}

	public function getMetaKey()
	{
		return $this->_metakey;
	}

	public function setMetaKey($metakey)
	{
		$this->_metakey = $metakey;
	}

	public function getMetaDesc()
	{
		return $this->_metadesc;
	}

	public function setMetaDesc($metadesc)
	{
		$this->_metadesc = $metadesc;
	}

	public function setMetaData($name, $content, $httpEquiv = false)
	{
		$name = strtolower($name);

		if($name == 'generator')
			$this->setGenerator($content);
		elseif ($name == 'keywords')
			$this->setMetakey($content);
		elseif ($name == 'description')
			$this->setMetadesc($content);
		else
		{
			$key = ($httpEquiv ? 'http-equiv' : 'name');
			$this->_metadata[] = '<meta ' . $key . '="' . $name . '" content="' . $content . '" />';
		}
	}

	public function setRawMetaData($metadata)
	{
		$this->_metadata[] = $metadata;
	}

	public function setBreadcrumb($breadcrumbs)
	{
		if (is_array($breadcrumbs) == false)
			$breadcrumbs = [$breadcrumbs];

		$this->_breadcrumbs = $breadcrumbs;
	}

	public function getBreadcrumb()
	{
		$count = count($this->_breadcrumbs);

		if ($count)
		{
			$label = SIDE == 'frontend' ? t('Home') : t('Dashboard');

			$html = '<ol class="breadcrumb">' . "\n";
			$html .= '<li>' . Html::link(Request::homeUrl(), $label) . '</li>' . "\n";

			for ($i = 0; $i < $count; ++$i)
			{
				$class = ($count - 1 == $i) ? ' class="active"' : '';
				$html .= '<li' . $class . '>' . $this->_breadcrumbs[$i] . '</li>' . "\n";
			}

			$html .= '</ol>' . "\n";

			return $html;
		}
	}

	public function render($view = null)
	{
		if  (is_null($view))
		{
			$action = str_replace('Action', '', ACTION);
			$view = strtolower(preg_replace('/[A-Z]/', '-$0', $action));
			$view = ltrim($view, '-');
		}
		else
			$view = trim($view);

		if (isset($this->form))
			static::$formVals = $this->form;

		if ($this->_pagetitle == '')
			$this->_pagetitle = $this->_title;

		if (SIDE == 'backend')
		{
			if ($this->_title)
				$this->_title .= ' • ' . \Config::get('sitename', 'Vanda');
			else
				$this->_title = \Config::get('sitename', 'Vanda');

			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			if (!$this->_title)
				$this->_title = \Config::get('sitename', 'Vanda');

			if (!$this->_metakey)
				$this->_metakey = \Config::get('metakey');

			if (!$this->_metadesc)
				$this->_metadesc = \Config::get('metadesc');
		}

		if (strpos($view, '.') or substr($view, 0, 1) != '/')
		{
			if (strpos($view, '.'))
			{
				$arr = explode('.', $view);
				$package = $arr[0];
				$subpackage = '';

				if (count($arr) > 2)
				{
					$subpackage = $arr[1] ? $arr[1] . '/' : '';
					$view = $arr[2];
				}
				else
					$view = $arr[1];
			}
			else
			{
				$package = PACKAGE;
				$subpackage = SUBPACKAGE ? SUBPACKAGE . '/' : '';
			}

			$paths = [
				//BASEPATH_TMP.'/cache/pages/'.$view.'.EXT',
				BASEPATH_PACKAGES . '/' . $package . '/' . SIDE . '/' . $subpackage . 'views/' . $view . '.php'
			];

			$path = File::getExactFilePath($paths);
		}
		else
			$path = ltrim($view, '/') . '.php';

		ob_start();
		$clearCache = false;

		if ($path)
			include $path;
		else
		{
			echo t('Cannot find the requested view') . ' "' . $view . '"';
			$clearCache = true;
		}

		$body = ob_get_clean();

		$cachePath = Cache::$cachePath;
		$cacheFile = Cache::$cacheFile;

		if ($clearCache)
			File::delete($cachePath . $cacheFile);
		elseif (Cache::$lifeTime > 0)
			file_put_contents($cachePath . $cacheFile, $body);

		if (is_null($this->_layout))
			$data = $body;
		else
		{
			$head = '<title>' . $this->_title . '</title>';

			if ($this->_generator)
				$head .= "\n\t\t" . '<meta name="generator" content="' . $this->_generator . '" />';

			if ($this->_metakey)
				$head .= "\n\t\t" . '<meta name="keywords" content="' . $this->_metakey . '" />';

			if ($this->_metadesc)
				$head .= "\n\t\t" . '<meta name="description" content="' . $this->_metadesc . '" />';

			$head .= implode("\n\t\t", $this->_metadata);

			if (File::exists(TEMPLATEPATH.'/assets/images/favicon.ico'))
				$head .= "\n\t\t".'<link rel="shortcut icon" href="'.Request::baseUrl() . '/' . TEMPLATEPATH . '/assets/images/favicon.ico" type="image/x-icon" />';

			// Don't use Request::homeUrl(). It will return empty in SPA mode.
			$head .= '
			<script type="text/javascript">
				var __vandaServerVars = {
					\'spa\': ' . (int)spa() . ',
					\'homeUrl\': "' . Uri::route() . '"
				};
			</script>' . "\n";

			if (trim($this->_layout) == '')
			{
				if ((SIDE == 'front' and FRONTEND_SPA_MODE) or (SIDE == 'backend' and BACKEND_SPA_MODE))
					$this->_layout = 'spa';
				else
					$this->_layout = 'index';
			}

			ob_start();
			$path = $this->_template . '/' . $this->_layout . '.php';

			if (File::exists($path))
				include $path;
			else
			{
				include $this->_template . '/index' . '.php';
				$body = t('Cannot find the requested layout') . ' "' . $this->_layout . '".' . $body;
			}

			foreach (Html::$addedCss as $css)
				$head .= "\t\t" . '<link rel="stylesheet" type="text/css" href="' . $css['url'] . $css['query'] . '" ' . $css['attribs'] . ' />' . "\n";

			foreach (Html::$addedJs as $js)
				$head .= "\t\t" . '<script type="text/javascript" src="' . $js['url'] . $js['query'] . '"></script>' . "\n";

			$template = ob_get_clean();

			$data = str_replace('{{head}}', $head, $template);
			$data = str_replace('{{title}}', $this->_pagetitle, $data);
			$data = str_replace('{{breadcrumb}}', $this->getBreadcrumb(), $data);
			$data = str_replace('{{main}}', $body, $data);
		}

		// Ensure there is {{flash}} tag in view. If not, loading view for datatable
		// will clear session for flash message before load main view from template.
		if (strpos($data, '{{flash}}') !== false)
		{
			$flashInfo = '';
			$flashSuccess = '';
			$flashWarning = '';
			$flashDanger = '';

			$tag = '<div class="alert alert-{{type}} alert-dismissable">
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">
						×
					</button>';

			if (is_array(Session::get('__vandaFlashInfo')))
			{
				$tagInfo = str_replace('{{type}}', 'info', $tag);

				$flashInfo .= $tagInfo;
				$flashInfo .= implode('</div>' . $tagInfo, Session::get('__vandaFlashInfo'));
				$flashInfo .= '</div>';
				Session::clear('__vandaFlashInfo');
			}

			if (is_array(Session::get('__vandaFlashSuccess')))
			{
				$tagSuccess = str_replace('{{type}}', 'success', $tag);

				$flashSuccess .= $tagSuccess;
				$flashSuccess .= implode('</div>' . $tagSuccess, Session::get('__vandaFlashSuccess'));
				$flashSuccess .= '</div>';
				Session::clear('__vandaFlashSuccess');
			}

			if (is_array(Session::get('__vandaFlashWarning')))
			{
				$tagWarning = str_replace('{{type}}', 'warning', $tag);

				$flashWarning .= $tagWarning;
				$flashWarning .= implode('</div>' . $tagWarning, Session::get('__vandaFlashWarning'));
				$flashWarning .= '</div>';
				Session::clear('__vandaFlashWarning');
			}

			if (is_array(Session::get('__vandaFlashDanger')))
			{
				$tagDanger = str_replace('{{type}}', 'danger', $tag);

				$flashDanger .= $tagDanger;
				$flashDanger .= implode('</div>' . $tagDanger, Session::get('__vandaFlashDanger'));
				$flashDanger .= '</div>';
				Session::clear('__vandaFlashDanger');
			}

			$flash = $flashInfo . $flashSuccess . $flashWarning . $flashDanger;
			$data = str_replace('{{flash}}', $flash, $data);
		}

		preg_match_all('/{{(.*)}}/U', $data, $positions);

		foreach ($positions[0] as $position)
		{
			$widgetData = '';

			$rows = DB::table('Widget')
					->where('position', $position)
					->where('side', SIDE)
					->where('status', 1)
					->sort('ordering')
					->loadAll();

			foreach ($rows as $row)
			{
				$arr = explode('.', $row->folder);
				$package = $arr[0];
				$widget = $arr[1];

				$widget = 'packages/' . $package . '/' . SIDE . '/widgets/' . $widget . '/' . $widget . '.php';

				if (is_file($widget))
				{
					$params = $row->params . '&id=' . $row->id;
					parse_str($params, $params);

					foreach ($params as $key => $value)
						$_GET['widget'][$key] = $value;
						//Request::set

					ob_start();
					include $widget;

					$classsuffix = (empty($params['classsuffix']) == false ? ' ' . $params['classsuffix'] : '');
					$widgetData .= '<div class="widget'.$classsuffix.'">';

					if (empty($params['showtitle']) == false)
						$widgetData .= '<h3>'.$row->title.'</h3>';

					$widgetData .= ob_get_clean();
					$widgetData .= '</div>';

					unset($_GET['widget']);
				}
			}

			$data = str_replace($position, $widgetData, $data);
		}

	    return $data;
	}

	public function display($view = null)
	{
		$data = $this->render($view);

		if (spa() and Request::isAjax())
		{
			$data = [
				'title' => $this->_title,
				'content' => $data,
				'redirect' => ''
			];

			$data = json_encode($data);
		}

		header('Expires: Mon, 27 Jul 1981 08:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');

		echo $data;
	}
}
