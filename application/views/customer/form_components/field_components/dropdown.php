<?php 
    $required = $is_required == 1 ? 'required' : '';
    $css_class_required = $is_required == 1 ? 'required' : '';
?>

<div class="w-100">
    <label for="<?= $form_field_name ?>" class="font-avenir"><?= $form_field_name ?></label>
    <?php if ($form_field_description) : ?>
        <p class="small"><?= $form_field_description ?></p>
    <?php endif; ?>
    <select type="text" class="form-select form-control-sm shadow-none" name="<?= $enc_field_name ?>" <?= $required ?>>
        <option hidden disabled selected></option>
        <?php foreach ($options as $option) : ?>
            <option value="<?= $option->option_name ?>"><?= $option->option_name ?></option>
        <?php endforeach; ?>
    </select>
</div> 