<?php

$checked = !empty($has_others_option) ? 'checked' : '';

?>
<hr>
<div class="row">
    <div class="col-6">
        <div class="">
            <label class="d-none" for="left_label"></label>
            <input type="number" class="form-control form-control-sm"  name="min_selection" placeholder="Minimum Selection" value="<?= $min_selection ?>">
        </div>
    </div>
    <div class="col-6">
        <div class="">
            <label class="d-none" for="right_label"></label>
            <input type="number" class="form-control form-control-sm"  name="max_selection" placeholder="Maximum Selection" value="<?= $max_selection ?>">
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 text-left">
        <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input"  name="has_others_option" <?= $checked ?>>
            <label class="form-check-label" for="has_others_option">Has 'Others' option</label>
        </div>
    </div>
</div>




