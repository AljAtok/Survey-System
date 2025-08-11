<?php

$checked = !empty($has_others_option) ? 'checked' : '';

?>

<hr>
<div class="row">
    <div class="col-12 text-left">
        <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input"  name="has_others_option" <?= $checked ?>>
            <label class="form-check-label" for="has_others_option">Has 'Others' option</label>
        </div>
    </div>
</div>

