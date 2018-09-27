<?php
defined('VD') or die('Access Denied');

use System\Html;
?>

<!DOCTYPE html>
<html>
	<head>
		{{head}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?=Html::css('bootstrap.min.css')?>
		<?=Html::css('font-awesome/css/font-awesome.css')?>
		<?=Html::css('animate.css')?>
		<?=Html::css('style.css')?>

		<!-- Mainly scripts -->
		<?=Html::js('jquery-2.1.1.js')?>
		<?=Html::js('bootstrap.min.js')?>

		<style type="text/css">
			.flash {
				max-width: 400px; margin-left: auto; margin-right: auto;
			}

			.flash div {
				text-align: center;
			}
		</style>
	</head>
	<body class="gray-bg">
		{{main}}
		<div class="flash">
			{{flash}}
		</div>
	</body>
</html>
