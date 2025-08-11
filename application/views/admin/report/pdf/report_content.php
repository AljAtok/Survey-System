<h1><?= $header->form_name ?></h1>
<?php if ($header->form_id == 1) : ?>
	<table>
		<tr>
			<td><label>Business Center: <?= $header->bc_name ?></label></td>	
			<td><label>Trip Num: <?= $header->trip_num ?></label></td>
		</tr>
		<tr>
			<td><label>Dressing Plant: <?= $header->store_loc_name ?></label></td>
			<td><label>Truck Vol: <?= $header->truck_vol ?></label></td>
		</tr>
		<tr>
			<td><label>Farm: <?= $header->farm_name ?></label></td>
			<td><label>ALW: <?= $header->alw ?></label></td>
		</tr>
	</table>
<?php else: ?>
	<table>
		<tr>
			<td><label>Business Center: <?= $header->bc_name ?></label></td>
			<td><label>Trip Num: <?= $header->trip_num ?></label></td>
		</tr>
		<tr>
			<td><label>Dressing Plant: <?= $header->store_loc_name ?></label></td>
			<td><label>ALW: <?= $header->alw ?></label></td>
		</tr>
		<tr>
			<td><label>Farm: <?= $header->farm_name ?></label></td>
		</tr>
	</table>
<?php endif;?>
<br><br>
<?php foreach ($categories as $category_row): ?>
		<h3><?=$category_row->form_field_category_sequence .'. ' . $category_row->form_field_category_name ?></h3>
	<?php foreach ($groups as $group_row): ?>
		<?php if ($group_row->form_field_category_id == $category_row->form_field_category_id): ?>
			<h5>
				<?=$group_row->form_field_category_sequence .'.' . $group_row->form_field_group_sequence . ' ' . $group_row->form_field_group_name ?>
				<small><?=(empty($group_row->form_field_group_description)) ? '' : '(' . ucwords($group_row->form_field_group_description) . ')' ?></small> 
			</h5>
		<?php foreach ($fields as $field_row): ?>
			<?php if ($group_row->form_field_group_id == $field_row->form_field_group_id): ?>

			<?php if ($field_row->form_field_name == 'clean_area_field_18' || $field_row->form_field_name == 'clean_area_field_35'): ?>
			<label>Moisture Pick-up Calculation</label><br>
			<?php endif; ?>
			<?php if ($field_row->form_field_name == 'clean_area_field_13' || $field_row->form_field_name == 'clean_area_field_31'): ?>
			<label for="">Weight of 5 Samples</label><br>
			<?php endif; ?>

			<?php $field_label = $field_row->form_field_label; ?>
			<label>
				<?= $field_label ?>&nbsp;:
				<small><?=(empty($field_row->form_field_description)) ? '' : '(' . ucwords($field_row->form_field_description) . ')' ?></small>
				<b><?= $field_row->form_header_detail_field_answer ?></b>
			</label><br>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endforeach; ?>