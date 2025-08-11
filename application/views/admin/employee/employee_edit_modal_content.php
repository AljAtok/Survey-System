
<input type="hidden" name="id" value="<?= encode($user->employee_id) ?>">
<div class="form-group">
    <label>Unit:</label>
    <select name="unit" class="form-control form-control-sm" data-placeholder="Select Unit" required>
        <option>Select Unit: </option>
        <?php foreach($units as $row):?>
        <?php $is_selected = ($row->unit_id == $user->unit_id) ? 'selected' : '' ?>
        <option value="<?=encode($row->unit_id)?>" <?= $is_selected ?> ><?=$row->unit_name?></option>
        <?php endforeach;?>
    </select>
</div>
<div class="form-group">
    <label>Location:</label>
    <select name="location" class="form-control form-control-sm" data-placeholder="Select Location" required>
        <option>Select Location: </option>
        <?php foreach($locations as $row):?>
        <?php $is_selected = ($row->location_id == $user->location_id) ? 'selected' : '' ?>
        <option value="<?=encode($row->location_id)?>" <?= $is_selected ?> ><?=$row->location_name?></option>
        <?php endforeach;?>
    </select>
</div>
<div class="form-group">
    <label for="fname">First Name: </label>
    <input type="text" id="fname" name="fname" class="form-control form-control-sm" value="<?= $user->employee_fname ?>" required>
    <div class="invalid-feedback">First Name is required.</div>
</div>
<div class="form-group">
    <label for="lname">Last Name: </label>
    <input type="text" id="lname" name="lname" class="form-control form-control-sm" value="<?= $user->employee_lname ?>" required>
    <div class="invalid-feedback">Last Name is required.</div>
</div>
<div class="form-group">
    <label for="mname">Middle Name / Initial: </label>
    <input type="text" id="mname" name="mname" class="form-control form-control-sm" value="<?=$user->employee_mname?>">
</div>
<div class="form-group">
    <label for="employee_no">Employee No: </label>
    <input type="text" id="employee_no" name="employee_no" class="form-control form-control-sm" value="<?= $user->employee_no ?>" required>
    <div class="invalid-feedback">Employee No is required.</div>
</div>
<div class="form-group">
    <label for="contact">Employee Contact: </label>
    <input type="number" id="contact" name="contact" class="form-control form-control-sm validate-contact" value="<?= $user->employee_contact ?>">
    <div class="invalid-feedback">Employee Contact is required.</div>
</div>
<div class="form-group">
    <label for="email">Employee Email: </label>
    <input type="email" id="email" name="email" class="form-control form-control-sm" value="<?= $user->employee_email ?>">
    <div class="invalid-feedback">Employee Email is required.</div>
</div>
<div class="form-group">
    <label>User Type:</label>
    <select name="employee_type" class="form-control form-control-sm" date-placeholder="Select User Type" required>
        <option value="">Select User Type: </option>
        <?php foreach($employee_types as $row):?>
        <?php $is_selected = ($row->employee_type_id == $user->employee_type_id) ? 'selected' : '' ?>
        <option value="<?=encode($row->employee_type_id)?>" <?=$is_selected?> ><?=$row->employee_type_name?></option>
        <?php endforeach;?>
    </select>
    <div class="invalid-feedback">User Type is required.</div>
</div>