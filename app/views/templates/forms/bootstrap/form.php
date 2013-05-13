<?php

$form = $this->form['build'];
$fields = $this->form['fields'];
$hidden = array();
$action = array();
$this->assets('script', '/js/form.js');
?>
<form id="<?= $form['fid']; ?>" action="<?= $form['action']; ?>" method="<?= $form['method']; ?>" class="<?= $form['css']; ?>">
<fieldset>
	<legend><?= $form['title']; ?></legend>
	<div class="alert" style="display: none; clear: both;"></div>
	<? foreach ($fields as $id => $field): ?>
	<?
	if (in_array($id, array('hidden', 'action'))):
		$$id = $field;
	else: 
	?>
	<div class="control-group">
		<? if (isset($field['label'])): ?>
		<label class="control-label" for="<?= $id; ?>"><?= $field['label']; ?></label>
		<? endif; ?>
		<div class="controls">
			<? if (is_array($field['field'])): ?>
			<? foreach($field['field'] as $index => $build): ?>
			<label><?= $build['label']; ?>&nbsp;<?= $build['field']; ?></label>
			<? endforeach; ?>
			<? else: ?>
			<?= $field['field']; ?>
			<? endif; ?>
			<? if (isset($field['helper'])): ?>
			<span class="help-inline" style="display: none;"></span>
			<? endif; ?>
			<? if (isset($field['helptext'])): ?>
			<div class="help-block"><?= $field['helptext']; ?></div>
			<? endif; ?>
		</div>
	</div>
	<? endif; ?>
	<? endforeach; ?>
	<? if (!empty($hidden)): ?>
	<div style="display: none;">
		<?= implode("\n", $hidden); ?>
	</div>
	<? endif; ?>
	<? if (!empty($action)): ?>
	<div class="form-actions">
		<?= implode("", $action); ?>
	</div>
	<? endif; ?>
</fieldset>
</form>
