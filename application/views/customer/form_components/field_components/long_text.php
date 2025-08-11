<?php 
    $required = $is_required == 1 ? 'required' : '';
    $css_class_required = $is_required == 1 ? 'required' : '';
?>
<div class="w-100 position-relative">
    <label for="<?= $form_field_name ?>" class="font-avenir"><?= $form_field_name ?></label>
    <?php if ($form_field_description) : ?>
        <p class="small"><?= $form_field_description ?></p>
    <?php endif; ?>
    <textarea class="form-control form-control-sm border-0 border-bottom border-dark rounded-0 shadow-none" name="<?= $enc_field_name ?>" style="height: 60px" <?= $required ?>></textarea>
</div> 