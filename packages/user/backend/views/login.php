<?php
defined('VD') or die('Access Denied');

use System\Form;
?>

<div class="middle-box text-center loginscreen animated fadeInDown">
	<div>
		<div>
			<h1 class="logo-name">VD</h1>
		</div>
		<h3>Welcome to <?=Config::get('sitename', 'Vanda')?></h3>
		<p>Login</p>

		<?=Form::open('user/login', 'class="m-t"')?>

			<?=Form::token()?>
			<?=Form::hidden('redirect', $this->redirect)?>

			<div class="form-group">
				<?=Form::text('username', '', 'placeholder="Username" autocomplete="off"')?>
			</div>
			<div class="form-group">
				<?=Form::password('password', '', 'placeholder="Password"')?>
			</div>
			<button type="submit" class="btn btn-primary block full-width m-b">Login</button>

		<?=Form::close()?>

		<p class="m-t"><small><strong>Copyright</strong> Vanda &copy; 2010-<?=date('Y')?></small></p>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#username').focus();
	});
</script>
