<?php
defined('VD') or die('Access Denied');

use System\Form;
use System\Toolbar;
?>

<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<?=Toolbar::add()?>
				<?php if (User::hasWritePermission('user')): ?>
					<?=Toolbar::activate()?>
					<?=Toolbar::deactivate()?>
					<?=Toolbar::delete()?>
				<?php endif; ?>
				<div class="ibox-tools">
					<form class="search-form">
						<div class="input-group">
							<?=Form::text('search', null, 'placeholder="' . t('Search') . '"')?>
							<span class="input-group-btn">
								<?=Form::buttonSubmit()?>
								<?=Form::buttonReset()?>
							</span>
						</div>
					</form>
				</div>
			</div>
			<div class="ibox-content">
				<div class="datatable">
					<?=$this->dataTable?>
				</div>
			</div>
		</div>
	</div>
</div>
