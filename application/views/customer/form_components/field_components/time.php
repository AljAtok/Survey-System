<?php 
    $required = $is_required == 1 ? 'required' : '';
    $css_class_required = $is_required == 1 ? 'required' : '';
?>

<div class="row w-100">    
    <?php if ($form_field_description) : ?>
        <div class="col-12">
            <p class="small"><?= $form_field_description ?></p>
        </div>
    <?php endif; ?>

    <label for="<?= $form_field_name ?>" class="col-4 col-form-label col-form-label-sm font-avenir"><?= $form_field_name ?></label>  
    <div class="col-8">
        <input type="time" class="form-control form-control-sm shadow-none" name="<?= $enc_field_name ?>" <?= $required ?>>
    </div>
</div>