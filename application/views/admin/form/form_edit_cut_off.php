<?php 
list($days, $hours) = explode('-', $row->expiration);

$count = ($days > 1) ? 'Days' : 'Day';

$meridiem = ($hours < 12) ? 'A.M' : 'P.M';
?>

<input type="hidden" name="id" value="<?= encode($row->form_id) ?>">
<div class="form-group">
    <label for="cut-off-day">Days:</label>
    <input type="number" id="cut-off-day" name="days" class="form-control form-control-sm" value="<?= $days ?>" min="0" max="30" required>
    <label for="cut-off-timeofday">Time of Day: 24h Format</label>
    <input type="number" id="cut-off-timeofday" name="timeofday" class="form-control form-control-sm" value="<?= $hours ?>" min="1" max="24" required>
    <div class="invalid-feedback"></div>
</div>