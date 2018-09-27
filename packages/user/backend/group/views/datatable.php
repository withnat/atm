<?php
defined('VD') or die('Access Denied');

use System\Html;
use System\Paginator;
use System\Uri;
?>

<div class="table-responsive">
	<div class="paginator-options">
		<?=Paginator::options()?>
	</div>
	<div class="paginator-detail">
		<?=Paginator::detail()?>
	</div>
	<div class="hidden-xs hidden-sm">
		<table class="table table-striped table-bordered table-hover" data-url="<?=Uri::route('user/group?datatable=1')?>">
			<thead>
				<tr>
					<?php if (User::hasWritePermission('user')): ?>
						<th class="text-center"><?=Html::toggleid()?></th>
					<?php endif; ?>
					<th class="searchable"><?=Paginator::sort(t('Name'), 'name')?></th>
					<th class="text-center"><?=Paginator::sort(t('Active'), 'status')?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->rows as $row): ?>
					<tr>
						<?php if (User::hasWritePermission('user')): ?>
							<td class="text-center"><?=Html::checkid($row->id)?></td>
						<?php endif; ?>
						<td><?=Html::link('user/group/form?id=' . $row->id, $row->name)?></td>
						<td class="text-center"><?=Html::active($row->status)?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="visible-xs visible-sm">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<?php foreach ($this->rows as $row): ?>
					<tr>
						<?php if (User::hasWritePermission('user')): ?>
							<td class="text-center"><?=Html::checkid($row->id)?></td>
						<?php endif; ?>
						<td>
							<label><?=t('Name')?> :</label> <?=Html::link('user/group/form?id=' . $row->id, $row->name)?><br />
							<label><?=t('Active')?> :</label> <?=Html::active($row->status)?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="paginator-link">
		<?=Paginator::link()?>
	</div>
</div>
