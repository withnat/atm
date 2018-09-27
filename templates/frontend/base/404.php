<?php
if (strpos($_SERVER['REQUEST_URI'], 'stock/fabrics'))
	$extra = ' onclick="closeoption();"';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
	<head>
		{{head}}
		<?php echo html::css('style.css'); ?>
	</head>
	<body>
		<div id="wrapper">
			<div id="wrapper_inner">
				<div id="left" <?php echo $extra; ?>>
					<a id="logo" href="<?php echo URI::base(); ?>" alt="Home" title="Home">Home</a>
					<div class="clear"></div>
					<ul>
						<li class="even"><?php echo html::link(URI::base(), 'Home'); ?></li>
						<li class="odd"><?php echo html::link('/stock/men', 'Men'); ?></li>
						<li class="even"><?php echo html::link('/stock/women', 'Women'); ?></li>
						<li class="odd"><?php echo html::link('/prices', 'Prices'); ?></li>
						<li class="even"><?php echo html::link('/stock/fabric', 'Fabrics'); ?></li>
						<li class="odd"><?php echo html::link('/reseller', 'Resellers'); ?></li>
						<li class="even"><?php echo html::link('/contact/appointment', 'Clients Visiting Bangkok'); ?></li>
						<li class="odd"><?php echo html::link('/contact', 'Contact Us'); ?></li>
					</ul>
					<?php if (CONTROLLER!='home'): ?>
						{{cart}}
					<?php endif; ?>
				</div>
				<div id="main">
					<div id="header" <?php echo $extra; ?>>
						<div id="usermenu">
							<?=html::link('/user', 'My Account')?> &nbsp;|&nbsp;
							<?=html::link('/stock/cart', 'My Cart')?> &nbsp;|&nbsp;
							<?=html::link('/stock/checkout', 'Checkout')?> &nbsp;|&nbsp;
							<?php if (User::loggedin()): ?>
								<?=html::link('/user/logout', 'Log Out')?>
							<?php else: ?>
								<?=html::link('/user/login', 'Log In')?>
							<?php endif; ?>
						</div>
						<?php echo form::create('/stock/product', 'name="frmSearch"'); ?>
							<input class="inputbox" name="search" /><?php echo html::image('btn_searchbutton.gif', null, array('align'=>'top', 'style'=>'margin-left:1px;', 'onclick'=>'document.getElementById(\'frmSearch\').submit();')); ?>
						<?php echo form::end(); ?>
					</div>
					<div id="content" <?php if (CONTROLLER=='home'): ?>class="home"<?php endif; ?>>
						{{message}}
						{{main}}
					</div>
				</div>
				<div class="clear"></div>
				<div id="footer" <?php echo $extra; ?>>
					<div id="copy">
						Copyright &copy; <?php echo date('Y'); ?> Bangkoktailorstore.com
					</div>
					<div id="menu">
						<?php echo html::link('/about', 'About Us'); ?> &nbsp;|&nbsp;
						<?php echo html::link('/privacy-policy', 'Privacy Policy'); ?> &nbsp;|&nbsp;
						<?php echo html::link('/faq', 'FAQ\'s'); ?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
