<?php

$form = $this->form['build'];
$fields = $this->form['fields'];
$hidden = array();
$action = array();
$this->assets->set('script', 'js/form.js');
?>
<form id="<?= $form['fid']; ?>" action="<?= $form['action']; ?>" method="<?= $form['method']; ?>" class="<?= $form['css']; ?>">
<fieldset>
	<? if ($form['title']): ?>
	<legend><?= $form['title']; ?></legend>
	<? endif; ?>
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
			<? if (isset($field['help'])): ?>
			<span class="help help-inline" style="display: none;"></span>
			<? endif; ?>
			<? if (isset($field['hint'])): ?>
			<div class="hint help-block"><?= $field['hint']; ?></div>
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
