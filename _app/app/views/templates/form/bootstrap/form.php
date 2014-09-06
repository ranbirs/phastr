<?php
$this->assets->script('assets/js/form/bootstrap.js');
?>
<form<?= \sys\utils\Html::attr($form['attr']); ?>>

    <div class="message"></div>

    <?php foreach ($form['fieldset'] as $fieldset_id => $fieldset) : ?>
        <fieldset id="<?= $fieldset_id; ?>">

            <?php if (isset($fieldset['title'])) : ?>
                <legend><?= $fieldset['title']; ?></legend>
            <?php endif; ?>

            <?php foreach ($fieldset['fields'] as $field_id) : ?>
            <?php $fields = $form['fields'][$field_id]; ?>
            <div class="form-group">
                <?php if (isset($fields['label']['value']) && $fields['label']['value']) : ?>
                <label class="control-label col-sm-2" for="<?= $field_id; ?>"><?= $fields['label']['value']; ?></label>

                <div class="controls col-sm-4">
                    <?php else : ?>
                    <div class="controls col-sm-12">
                        <?php endif; ?>
                        <?php foreach ($fields['field'] as $field) : ?>
                            <?php include $field['control'] . '.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

        </fieldset>
    <?php endforeach; ?>

    <?php if ($form['hidden']) : ?>
        <div style="display: none;">
            <?php foreach ($form['hidden'] as $id => $hidden) : ?>
                <?php include $hidden['control'] . '.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($form['button']) : ?>
        <div class="form-actions">
            <?php foreach ($form['button'] as $id => $button) : ?>
                <?php include $button['control'] . '.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</form>
