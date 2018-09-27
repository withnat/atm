<?php
namespace System;

use System\Cookie;
use System\Html;
use System\Request;
use System\Session;

class Paginator_bak
{
	public $total;
	public $page;
	public $pagesize;
	public $pagecount;
	public $pagenumstart;
	public $pagenumend;

	public $sortcolumn;
	public $sortway;

	public static function dataTablesJs()
	{
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.dataTables').DataTable({
					columns: [
						{data: 'id', className: 'text-center', orderable: false},
						{data: 'name'},
						{data: 'username'},
						{data: 'email'},
						{data: 'usergroup'},
						{data: 'status', className: 'text-center'},
						{data: 'visited'},
						{data: 'created'}
					],
					//order: [[0, 'asc']],
					filter: false,
					stateSave: true,
					responsive: false,
					processing: true,
					serverSide: true,
					ajax: {
						url: '<?=Uri::route('user/list')?>',
						data: function(settings){
							//var settings = $('.dataTables').dataTable().fnSettings();
							var data = $('.search-form').serializeArray();
							data.push(
								{name: 'draw', value: settings.draw},
								{name: 'start', value: settings.start},
								{name: 'length', value: settings.length},
								{name: 'sortcol', value: settings.columns[settings.order[0].column].data},
								{name: 'sortdir', value: settings.order[0].dir}
							);
							return data;
						}
					},
					fnDrawCallback: function(){
						__vandaPaginatorDataTables();
					},
					dom: '<"html5buttons"B>lTfgitp',
					buttons: [
						{extend: 'csv', title: '<?//=$this->getTitle()?>'},
						{extend: 'excel', title: '<?//=$this->getTitle()?>'},
						{extend: 'pdf', title: '<?//=$this->getTitle()?>'},
						{extend: 'print',
							customize: function(win){
							$(win.document.body).addClass('white-bg');
							$(win.document.body).css('font-size', '10px');
							$(win.document.body).find('table')
							.addClass('compact')
							.css('font-size', 'inherit');
						}
						}
					]
				});

				$('.search-form input').keyup(function(){
					$('.dataTables').DataTable().draw();
				});

				$('.search-form select').change(function(){
					$('.dataTables').DataTable().draw();
				});
			});
		</script>
	<?php
	}

	public function __construct($total=null, $page=null, $pagesize=null)
	{
		/*
		$this->total = $total;
		$this->page = $page ? $page : $this->_get('page');
		$this->pagesize = $pagesize ? $pagesize : $this->_get('pagesize');

		$pagecount = (int)($this->total/$this->pagesize);

		if ($this->total % $this->pagesize > 0) ++$pagecount;
		if ($this->page == 1) {$num = 1;} else {$num=(($this->page-1)*$this->pagesize)+1;}

		$pagenumstart = (($this->page-1)*$this->pagesize)+1;
		$pagenumend = $this->page*$this->pagesize;
		if ($pagenumend > $this->pagecount) {$pagenumend = $pagenumend-(($this->pagesize*$this->page)-$pagecount);}

		$this->pagecount = $pagecount;
		$this->pagenumstart = $pagenumstart;
		$this->pagenumend = $pagenumend;

		$this->sortcolumn = $this->_get('sc');
		$this->sortway = $this->_get('sw');
		*/

		$data = $_GET;
	}

	public function prepare()
	{
		$this->page = $this->page ? $this->page : $this->get('page');
		$this->pagesize = $this->pagesize ? $this->pagesize : $this->get('pagesize');

		$pagecount = (int)($this->total/$this->pagesize);

		if ($this->total % $this->pagesize > 0) ++$pagecount;
		if ($this->page == 1) {$num = 1;} else {$num=(($this->page-1)*$this->pagesize)+1;}

		$pagenumstart = (($this->page-1)*$this->pagesize)+1;

		if ($this->page == $pagecount)
			$pagenumend = $this->total;
		elseif ($this->page < $pagecount)
			$pagenumend = $this->page*$this->pagesize;
		else
			$pagenumend = 1;

		$this->pagecount = $pagecount;
		$this->pagenumstart = $pagenumstart;
		$this->pagenumend = $pagenumend;

		$this->sortcolumn = $this->get('sc');
		$this->sortway = $this->get('sw');
	}

	public static function get($name, $default=null)
        {
		$side = SIDE;
		$package = PACKAGE;
		$subpackage = (SUBPACKAGE ? SUBPACKAGE : 'default');
		$action = ACTION;

		$cookieContext = preg_replace('/[^a-zA-Z0-9]+/', '', Request::basePath());
		$sessionContext = $side.$package.$subpackage.$action;

                if ($name == 'page')
		{
			if (Request::get('page'))
			{
				$value = Request::get('page');
				Session::set($sessionContext.'page', Request::get('page'));
			}
			else
				$value = Session::get($sessionContext.'page');

			if (empty($value))
			{
				if ($default)
					$value = $default;
				else
					$value = 1;
			}
		}
                elseif ($name == 'pagesize')
                {
			$ck = Cookie::get($cookieContext.'Paginator');
			$value = null;
			if (is_array($ck))
			{
				if (isset($ck[$side][$package][$subpackage][$action]['pagesize']))
					$value = $ck[$side][$package][$subpackage][$action]['pagesize'];
			}

                        if (empty($value))
                        {
                                if ($default)
                                        $value = $default;
                                else
                                        $value = 20;
                        }
                }
		elseif ($name == 'sc')
		{
			$ck = Cookie::get($cookieContext.'Paginator');
			$value = null;
			if (is_array($ck))
			{
				if (isset($ck[$side][$package][$subpackage][$action]['sc']))
					$value = $ck[$side][$package][$subpackage][$action]['sc'];
			}

                        if (empty($value))
                        {
                                if ($default)
                                        $value = $default;
                        }
		}
		elseif ($name == 'sw')
		{
			$ck = Cookie::get($cookieContext.'Paginator');
			$value = null;
			if (is_array($ck))
			{
				if (isset($ck[$side][$package][$subpackage][$action]['sw']))
					$value = $ck[$side][$package][$subpackage][$action]['sw'];
			}

                        if (empty($value))
                        {
                                if ($default)
                                        $value = $default;
                                else
                                        $value = 'ASC';
                        }
		}

		return $value;
	}

	public function sort($title, $fieldName)
	{
		$sw = ($this->sortway == 'ASC' ? 'DESC' : 'ASC');
		$sorting = 'Sorting: '.strip_tags($title);

		$html = null;
		if ($fieldName == $this->sortcolumn)
		{
			if ($this->sortway == 'ASC')
			{
				$sorting .= ' (in descending order)';
				$html = html::image('pagination/sort_up.png');
			}
			else
			{
				$sorting .= ' (in ascending order)';
				$html = html::image('pagination/sort_down.png');
			}
		}

		$html .= '<a href="#" onclick="javascript:sortpage(\''.PACKAGE.'\', \''.SUBPACKAGE.'\', \''.ACTION.'\', \''.$fieldName.'\', \''.$sw.'\'); return false;" title="'.$sorting.'">'.$title.'</a>';
		return $html;
	}

	public function render()
	{
		$url = PACKAGE.
			(SUBPACKAGE ? '/'.SUBPACKAGE : '').
			(ACTION ? '/'.ACTION : '');

		if ($this->page > 1)
		{
			$first = html::link($url.'?page=1', html::image('pagination/first.png'));
			$prev = html::link($url.'?page='.($this->page-1), html::image('pagination/prev.png'));
		}
		else
		{
			$first = html::image('pagination/first_dis.png');
			$prev = html::image('pagination/prev_dis.png');
		}

		if ($this->pagecount > $this->page)
		{
			$next = html::link($url.'?page='.($this->page+1), html::image('pagination/next.png'));
			$last = html::link($url.'?page='.$this->pagecount, html::image('pagination/last.png'));
		}
		else
		{
			$next = html::image('pagination/next_dis.png');
			$last = html::image('pagination/last_dis.png');
		}

		$page = '';

		$show_first_dot= false;
		$show_last_dot = false;

		if ($this->pagecount <= 9)
		{
			$i = 1;
			$k = $this->pagecount;
		}
		else
		{
			if ($this->page <= 5)
			{
				$i = 1;
				$k = 9;
				$show_last_dot = true;
			}
			elseif ($this->pagecount - $this->page <= 4)
			{
				$i = $this->pagecount - 8;
				$k = $this->pagecount;
				$show_first_dot = true;
			}
			else
			{
				$i = $this->page - 4;
				$k = $this->page + 4;
				$show_first_dot = true;
				$show_last_dot = true;
			}
		}

		if ($show_first_dot)
			$page .= '<strong>...</strong>';

		for ($i; $i<=$k; ++$i)
		{
			if ($this->page == $i)
				$page .= '<span class="page_current">'.$i.'</span>';
			else
			{
				$url = PACKAGE.
					(SUBPACKAGE ? '/'.SUBPACKAGE : '').
					(ACTION ? '/'.ACTION : '');
				$page .= '<span class="page_link">'.html::link($url.'?page='.$i, $i).'</span>';
			}
		}

		if ($show_last_dot)
			$page .= '<strong>...</strong>';

		$side = SIDE;
		$package = PACKAGE;
		$subpackage = (SUBPACKAGE ? SUBPACKAGE : 'default');
		$action = ACTION;

		$html = '<table class="pagination">
			<tr>
				<td>'.$first.'</td>
				<td>'.$prev.'</td>
				<td>'.$page.'</td>
				<td>'.$next.'</td>
				<td>'.$last.'</td>
				<td>&nbsp;|&nbsp;</td>
				<td>'.t('Records').':</td>
				<td>
					<select class="select" onchange="var val = this[selectedIndex].value;
						var exdate=new Date();
						exdate.setDate(exdate.getDate()+365);
						document.cookie = \''.preg_replace('/[^a-zA-Z0-9]+/', '', Request::basePath()).'Paginator['.$side.']['.$package.']['.$subpackage.']['.$action.'][pagesize]=\'+val+\'; path=/; expires=\'+exdate.toGMTString();
						window.location.reload();">
						<option value="5" '.($this->pagesize==5 ? 'selected' : '').'>5</option>
						<option value="10" '.($this->pagesize==10 ? 'selected' : '').'>10</option>
						<option value="15" '.($this->pagesize==15 ? 'selected' : '').'>15</option>
						<option value="20" '.($this->pagesize==20 ? 'selected' : '').'>20</option>
						<option value="25" '.($this->pagesize==25 ? 'selected' : '').'>25</option>
						<option value="30" '.($this->pagesize==30 ? 'selected' : '').'>30</option>
						<option value="50" '.($this->pagesize==50 ? 'selected' : '').'>50</option>
						<option value="100" '.($this->pagesize==100 ? 'selected' : '').'>100</option>
						<option value="1000000" '.($this->pagesize==1000000 ? 'selected' : '').'>'.t('All').'</option>
					</select>
				</td>
				<td width=100%" align="right">Page '.number_format($this->page).'/'.number_format($this->pagecount).' | '.t('View').' '.number_format($this->pagenumstart).' - '.number_format($this->pagenumend).' '.t('Of').' '.number_format($this->total).'</td>
			</tr>
		</table>';

		return $html;
	}
}
