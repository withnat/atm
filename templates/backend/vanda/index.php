<?php
defined('VD') or die('Access Denied');

use System\Arr;
use System\Auth;
use System\DB;
use System\Html;
use System\Request;
use System\Uri;

function activeMenu($packageSets = [])
{
	$value = PACKAGE . (SUBPACKAGE ? '.' . SUBPACKAGE : '') . '.' . ACTION;

	if (Arr::has($packageSets, $value, true))
		return 'active';
}

$avatar = DB::table('User')->where(Auth::identity()->id)->load()->imageAvatar;
?>

<!DOCTYPE html>
<html>
	<head>
		{{head}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<?=Html::css('bootstrap.min.css')?>
		<?=Html::css('font-awesome/css/font-awesome.css')?>
		<?=Html::css('animate.css')?>
		<?=Html::css('plugins/switchery/switchery.css')?>
		<?=Html::css('plugins/iCheck/custom.css')?>
		<?=Html::css('plugins/summernote/summernote.css')?>
		<?=Html::css('plugins/summernote/summernote-bs3.css')?>
		<?php//=Html::css('plugins/nouslider/jquery.nouislider.css')?>
		<?=Html::css('plugins/nouslider/nouislider.min.css')?>
		<?=Html::css('plugins/datapicker/datepicker3.css')?>
		<?=Html::css('plugins/toastr/toastr.min.css')?>
		<!--
		Load the toastr css before load the inspinia css.
		This way Inspinia overwrites the toastr css with its own custom css
		and prevent duplicated icons with toastr.
		-->

		<!-- Mainly scripts -->
		<?=Html::js('jquery-2.1.1.js')?>
		<?=Html::js('bootstrap.min.js')?>
		<?=Html::js('plugins/metisMenu/jquery.metisMenu.js')?>
		<?=Html::js('plugins/slimscroll/jquery.slimscroll.min.js')?>

		<!-- Custom and plugin javascript -->
		<?=Html::js('template.js?v=0.1')?>
		<?=Html::js('plugins/pace/pace.min.js')?>
		<?=Html::js('plugins/switchery/switchery.js')?>
		<?=Html::js('plugins/iCheck/icheck.min.js')?>
		<?=Html::js('plugins/summernote/summernote.min.js')?>
		<?php//=Html::js('plugins/nouslider/jquery.nouislider.min.js')?>
		<?=Html::js('plugins/nouslider/nouislider.min.js')?>
		<?=Html::js('plugins/datapicker/bootstrap-datepicker.js')?>
		<?=Html::js('plugins/toastr/toastr.min.js')?>

		<?php//=Html::js('plugins/easypiechart/jquery.easypiechart.js')?>
		<?=Html::js('plugins/flot/jquery.flot.js')?>
		<?=Html::js('plugins/flot/jquery.flot.tooltip.min.js')?>
		<?=Html::js('plugins/flot/jquery.flot.spline.js')?>
		<?=Html::js('plugins/flot/jquery.flot.resize.js')?>
		<?=Html::js('plugins/flot/jquery.flot.pie.js')?>
		<?=Html::js('plugins/flot/jquery.flot.symbol.js')?>
		<?=Html::js('plugins/flot/jquery.flot.time.js')?>

		<?=Html::js('plugins/masonry/masonry.pkgd.min.js')?>
		<?=Html::js('plugins/validation/dist/jquery.validate.min.js')?>
		<?=Html::js('plugins/Bootbox/bootbox.min.js')?>

		<?php //Html::js('plugins/jquery-ui/jquery-ui.min.js')?>
		<?php //Html::css('templates/backend/vanda/assets/js/plugins/jquery-ui/jquery-ui.min.css')?>

		<?php //Html::js('plugins/combobox/combobox.js')?>

		<?=Html::js('plugins/loadingoverlay/src/loadingoverlay.min.js')?>

		<?=Html::js('js.cookie.js')?>
		<?=Html::js('spa.js')?>
		<?=Html::js('vanda.js?v=0.1')?>
		<?=Html::js('system.js?v=0.91')?>
		<?=Html::js('nokia.js?v=0.7')?>

		<?=Html::css('plugins/chosen/chosen.css')?>
		<?=Html::js('plugins/chosen/chosen.jquery.js')?>

		<?=Html::css('style.css?v=0.2')?>

		<?php//=Html::js('plugins/wnumb/wNumb.js')?>

		<style type="text/css">
			.breadcrumb .active {
				font-weight: bold;
			}

			.i-checks label {
				font-weight: normal;
			}

			th.searchable span {
				border-bottom: 1px dashed #bbb;
			}

			th.exact-searchable span {
				border-bottom: 1px solid #ddd;
			}

			.ibox-tools {
				float: right !important;
			}

			.input-group-btn {
				display: inline;
			}

			.ibox-tools .search-form {
				margin: 0;
			}

			.ibox-tools .search-form .form-control {
				display: inline;
				width: auto;
			}

			select.input-sm {
				line-height: 10px;
			}

			form label.required {
				padding-right: 6px;
			}

			form label.required:after {
				content: " *";
			}

			th span.sort {
				cursor: pointer;
			}

			.navbar-default li span.separator {
				/*margin:4px 25px 2px 25px;*/
				margin:4px 0px 2px 0px;
				border-bottom: 2px solid #293846;
				display: block;
			}

			.navbar-default li li span.separator {
				/*margin:4px 25px 2px 50px;*/
				margin:4px 0px 2px 0px;
				border-bottom: 1px solid #2f4050;
				display: block;
			}

			.navbar-default a {
				outline: none;
			}

			.search-form input, .search-form select {
				height: 30px;
				line-height: 1.5;
			}

			.paginator-options select {
				text-align: center;
				width: 75px;
				height: 30px;
				margin-left: 5px;
				line-height: 1.5;
				display: inline;
			}

			.paginator-link {
				text-align: right;
				margin: 0;
			}

			.paginator-link ul {
				margin: 0;
			}

			.datatable {
				margin-bottom: 8px;
			}

			.form-control {
				max-width: 300px;
			}

			.preview {
				position: relative;
				overflow: hidden;
				max-width: 300px; /* Auto resize for small mobile screen */
				height: 300px; /* Need fixed space for overflow */
				border: 1px solid #e5e6e7;
			}

			canvas {
				display: none;
			}

			.preview img {
				max-width: 100%;
				top: -9999px;
				bottom: -9999px;
				left: -9999px;
				right: -9999px;
				margin: auto;
				position: absolute;
			}
			
			.preview .delete {
				cursor: pointer;
				padding: 2px;
				right: 0;
				z-index: 100;
				position: absolute;
				background-color: rgba(255, 255, 255, 0.5);

			}

			.preview .delete:hover {
				background-color: rgba(255, 255, 255, 0.7);
			}

			.profile-element img.img-circle {
				max-width: 48px;
				max-height: 48px;
			}

			.file {
				border: none;
				margin-bottom: 10px;
			}

			/* Hide border for xs view */
			.table-responsive {
				border: none;
			}

			.datatable .label {
				font-weight: normal;
			}

			.datepicker {
				max-width:261px
			}

			.ibox-title button {
				padding: 5px 10px;
				font-size: 12px;
				line-height: 1.5;
			}

			.ibox-title.tool {
				text-align: right;
				border: 1px solid #e7eaec;
			}

			.ibox-title {
				min-height: 58px;
				border:1px solid #e7eaec;
			}

			.ibox-content {
				border:1px solid #e7eaec;
			}

			form .ibox-title {
				min-height: 45px;
			}

			form .ibox-content .ibox-title {
				border: none;
			}

			.search-form .hidden-xs,
			.search-form .hidden-sm {
				display: inline !important;
			}

			#btn-detect {
				float: right;
			}

			#map {
				margin-bottom: 18px;
				clear: both;
			}
			
			/* col-md */
			@media (max-width: 991px) {
				.search-form .hidden-xs,
				.search-form .hidden-sm {
					display: none!important;
				}

				/* Need space for tool button ie enable/disable/delete */
				.ibox-tools {
					float: none !important;
				}
			}

			/* col-xs */
			@media (max-width: 767px) {
				#page-wrapper {
					padding: 0 2px;
				}

				.page-heading {
					margin: 0;
				}

				.wrapper-content {
					padding: 20px 0 40px;
				}
			}

			/* Modal box by BootBox. Override Summernote css */
			.modal-body,
			.modal-footer{
				padding: 15px;
				margin: 0;
			}

			.error {
				/*float: right;*/
			}
		</style>

		<script type="text/javascript">
			$(document).ready(function() {
				$('.search-form button.submit').html('<i class="fa fa-search"></i>');
				$('.search-form button.reset').html('<i class="fa fa-refresh"></i>');

				$('.ui-autocomplete-input').css('color','#555');
				$('.ui-autocomplete-input').css('font-size','14px');
				$('.ui-autocomplete-input').css('font-family','"Helvetica Neue",Helvetica,Arial,sans-serif');
				$('.ui-autocomplete-input').css('text-indent','18px');
				$('.ui-autocomplete-input').css('width','436px');
				$('.ui-autocomplete-input').css('height','32px');

				$('.ui-button-icon-only').css('width','13px');
				$('.ui-button').css('margin-top','-2px');
				$('.ui-button-text').css('padding','15px 0px');
			});
		</script>
	</head>
	<body>

		<div id="wrapper">

			<nav class="navbar-default navbar-static-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav metismenu" id="side-menu">
						<li class="nav-header">
							<!--
							<div class="dropdown profile-element">
								<span>
									<?php if ($avatar): ?>
										<img src="<?=$avatar?>" class="img-circle" />
									<?php endif; ?>
								</span>
								<a data-toggle="dropdown" class="dropdown-toggle">
									<span class="clear">
										<span class="block m-t-xs">
											<strong class="font-bold"><?=Auth::identity()->name?></strong>
										</span>
										<span class="text-muted text-xs block">
											@<?=Auth::identity()->username?><b class="caret"></b>
										</span>
									</span> 
								</a>
								<ul class="dropdown-menu animated fadeInRight m-t-xs">
									<li><?=Html::link('user/profile', t('My Profile'))?></li>
									<li><a href="<?=Uri::route('user/logout')?>"><?=t('Log out')?></a></li>
								</ul>
							</div>
							-->
							<div class="logo-element">
								IF
							</div>
						</li>
						<li>
							<a href="<?=Request::homeUrl()?>">
								<i class="fa fa-th-large"></i>
								<span class="nav-label"><?=t('Withdrawal')?></span>
							</a>
						</li>

						<li class="<?=activeMenu(['config.indexAction'])?>">
							<?=Html::link('config', '<i class="fa fa-gear"></i><span class="nav-label">' . t('Settings') . '</span>')?>
						</li>

						<!--
						<?php if (User::hasPermission('evaluate')): ?>
							<li class="customer-index <?=activeMenu([
								'evaluate.indexAction',
								'evaluate.formAction'
							])?>">
								<?=Html::link('evaluate', '<i class="fa fa-male"></i><span class="nav-label">' . t('Evaluate') . '</span>')?>
							</li>
						<?php endif; ?>

						<?php if (User::hasPermission('report')): ?>
							<li class="customer-index <?=activeMenu([
								'report.indexAction',
							])?>">
								<?=Html::link('report', '<i class="fa fa-square"></i><span class="nav-label">' . t('Report') . '</span>')?>
							</li>
						<?php endif; ?>

						<?php if (User::hasPermission('user')): ?>
							<li class="user <?=activeMenu([
								'user.indexAction',
								'user.formAction',
								'user.group.indexAction',
								'user.group.formAction'
							])?>">
								<a>
									<i class="fa fa-user"></i>
									<span class="nav-label"><?=t('Users')?></span>
									<span class="fa arrow"></span>
								</a>
								<ul class="nav nav-second-level collapse">
									<li class="user-index <?=activeMenu(['user.indexAction', 'user.formAction'])?>"><?=Html::link('user', t('User List'))?></li>
									<li class="user-group <?=activeMenu(['user.group.indexAction', 'user.group.formAction'])?>"><?=Html::link('user/group', t('User Groups'))?></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (User::hasPermission('master')): ?>
							<li class="<?=activeMenu([
								'department.indexAction',
								'department.formAction'
							])?>">
								<a>
									<i class="fa fa-cube"></i>
									<span class="nav-label"><?=t('Master Data')?></span>
									<span class="fa arrow"></span>
								</a>
								<ul class="nav nav-second-level collapse">
									<li class="<?=activeMenu(['department.indexAction', 'department.formAction'])?>"><?=Html::link('department', t('Departments'))?></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (User::hasPermission('config')): ?>
							<li class="<?=activeMenu(['config.indexAction'])?>">
								<?=Html::link('config', '<i class="fa fa-gear"></i><span class="nav-label">' . t('Settings') . '</span>')?>
							</li>
						<?php endif; ?>
						-->
					</ul>
				</div>
			</nav>

			<div id="page-wrapper" class="gray-bg">

				<div class="row border-bottom">
					<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
						<div class="navbar-header">
							<a class="navbar-minimalize minimalize-styl-2 btn btn-primary "><i class="fa fa-bars"></i> </a>
							<form role="search" class="navbar-form-custom" method="post">
								<div class="form-group">
									<!--<input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">-->
								</div>
							</form>
						</div>
						<ul class="nav navbar-top-links navbar-right">
							<li>
								<span class="m-r-sm text-muted welcome-message">Welcome to <?=Config::get('sitename', 'Vanda')?></span>
							</li>
							<!--
							<li class="dropdown">
								<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
									<i class="fa fa-bell"></i> <span class="label label-primary">8</span>
								</a>
								<ul class="dropdown-menu dropdown-alerts">
									<li>
										<a href="mailbox.html">
											<div>
												<i class="fa fa-envelope fa-fw"></i> 10 Submitted from Handler
												<span class="pull-right text-muted small">4 minutes ago</span>
											</div>
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="profile.html">
											<div>
												<i class="fa fa-twitter fa-fw"></i> 3 In progress
												<span class="pull-right text-muted small">12 minutes ago</span>
											</div>
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="grid_options.html">
											<div>
												<i class="fa fa-upload fa-fw"></i> 1 Help needed
												<span class="pull-right text-muted small">4 minutes ago</span>
											</div>
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<div class="text-center link-block">
											<a href="notifications.html">
												<strong>See All Alerts</strong>
												<i class="fa fa-angle-right"></i>
											</a>
										</div>
									</li>
								</ul>
							</li>
							<li>
								<a href="<?=Uri::route('user/profile')?>">
									<i class="fa fa-user"></i> <?=t('My Profile')?>
								</a>
							</li>
							<li>
								<a href="<?=Uri::route('user/logout')?>">
									<i class="fa fa-sign-out"></i> <?=t('Log out')?>
								</a>
							</li>
							-->
						</ul>

					</nav>
				</div>

				<div class="content">
					<div class="row wrapper border-bottom white-bg page-heading">
						<div class="col-lg-10">
							<h2 class="title">{{title}}</h2>
							<div class="breadcrumb">{{breadcrumb}}</div>
						</div>
						<div class="col-lg-2">
						</div>
					</div>
					<div class="wrapper wrapper-content animated fadeInRight">
						<div class="flash">{{flash}}</div>
						<div class="main">{{main}}</div>
						<!--
						<div class="small-chat-box fadeInRight animated">

							<div class="heading" draggable="true">
								<small class="chat-date pull-right">
									02.19.2015
								</small>
								Small chat
							</div>

							<div class="content">

								<div class="left">
									<div class="author-name">
										Monica Jackson <small class="chat-date">
											10:02 am
										</small>
									</div>
									<div class="chat-message active">
										Lorem Ipsum is simply dummy text input.
									</div>

								</div>
								<div class="right">
									<div class="author-name">
										Mick Smith
										<small class="chat-date">
											11:24 am
										</small>
									</div>
									<div class="chat-message">
										Lorem Ipsum is simpl.
									</div>
								</div>
								<div class="left">
									<div class="author-name">
										Alice Novak
										<small class="chat-date">
											08:45 pm
										</small>
									</div>
									<div class="chat-message active">
										Check this stock char.
									</div>
								</div>
								<div class="right">
									<div class="author-name">
										Anna Lamson
										<small class="chat-date">
											11:24 am
										</small>
									</div>
									<div class="chat-message">
										The standard chunk of Lorem Ipsum
									</div>
								</div>
								<div class="left">
									<div class="author-name">
										Mick Lane
										<small class="chat-date">
											08:45 pm
										</small>
									</div>
									<div class="chat-message active">
										I belive that. Lorem Ipsum is simply dummy text.
									</div>
								</div>


							</div>
							<div class="form-chat">
								<div class="input-group input-group-sm"><input type="text" class="form-control"> <span class="input-group-btn"> <button
											class="btn btn-primary" type="button">Send
                </button> </span></div>
							</div>

						</div>
						<div id="small-chat" style="margin-bottom:20px;">

							<span class="badge badge-warning pull-right">5</span>
							<a class="open-small-chat">
								<i class="fa fa-comments"></i>

							</a>
						</div>
						-->
					</div>
				</div>

				<div class="footer">
					<div class="pull-right">
						<a href="http://vanda.io" style="color: inherit;">vanda.io</a>
					</div>
					<div>
						<strong>Copyright</strong> Vanda &copy; 2010-<?=date('Y')?>
					</div>
				</div>

		    </div>
		</div>
	</body>
</html>
