<?php
defined('VD') or die('Access Denied');

use System\Form;
use System\Request;

$backUrl = Request::homeUrl();
?>

<?=Form::open('user/saveprofile')?>
<?=Form::token()?>
<?=Form::hidden('id')?>

<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<?=Form::buttonClose(null, 'onclick="window.location.href=\'' . $backUrl . '\';"')?>
				<?=Form::buttonSave()?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('My Profile Information')?></h5>
			</div>
			<div class="ibox-content">
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Name')?></label>
					<div class="col-lg-10">
						<?=Form::text('name')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Username')?></label>
					<div class="col-lg-10">
						<?=Form::text('username')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Password')?></label>
					<div class="col-lg-10">
						<?=Form::password('password')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Confirm Password')?></label>
					<div class="col-lg-10">
						<?=Form::password('passwordConfirm')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Email')?></label>
					<div class="col-lg-10">
						<?=Form::email('email')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Language')?></label>
					<div class="col-lg-10">
						<?=Form::select('languageId')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Avatar')?></label>
					<div class="col-lg-10 control-label">
						<?=Form::hidden('imageAvatar')?>
						<canvas id="imageAvatarCanvas"></canvas>
						<input type="file" id="imageAvatarFile" class="file" />
						<div id="imageAvatarPreview" class="preview text-left">
							<div id="deleteImageAvatarFile" class="delete fa fa-2x fa-times"></div>
							<?php if (@$this->form->imageAvatar): ?>
								<img src="<?=$this->form->imageAvatar?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<?=Form::close()?>
