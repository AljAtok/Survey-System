<hr>
<div class="row">
    <div class="col-4">
        <select class="form-control form-control-sm" name="min_value">
            <?php 
                $default_value = 1;
                for ($x = 0; $x <= 1; $x++) {
                    $selected = (isset($min_value) && $x == $min_value) ? 'selected' : (($x == $default_value) ? 'selected' : '');
                    echo "<option value=\"$x\" $selected>$x</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-4 text-center">
        <p>To</p>
    </div>
    <div class="col-4">
        <select class="form-control form-control-sm" name="max_value">
            <?php 
                $default_value = 5;
                for ($x = 2; $x <= 10; $x++) {
                    $selected = (isset($max_value) && $x == $max_value) ? 'selected' : (($x == $default_value) ? 'selected' : '');
                    echo "<option value=\"$x\" $selected>$x</option>";
                }
            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="">
            <label class="d-none" for="left_label"></label>
            <input type="text" class="form-control form-control-sm"  name="left_label" placeholder="Left Label" value="<?= $left_label ?>">
        </div>
    </div>
    <div class="col-6">
        <div class="">
            <label class="" for="right_label"></label>
            <input type="text" class="form-control form-control-sm"  name="right_label" placeholder="Right Label" value="<?= $right_label ?>" >
        </div>
    </div>
</div>



