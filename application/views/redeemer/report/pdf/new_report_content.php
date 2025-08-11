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
<table class="table table-bordered">
	<thead class="thead-dark">
		<tr class="text-center">
			<th>Field</th>
			<th>Answer</th>
			<th>Standard</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($categories as $category_row): ?>
	<tr>
		<td class="font-weight-bold"><?=$category_row->form_field_category_sequence .'. ' . $category_row->form_field_category_name ?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<?php foreach ($groups as $group_row): ?>
		<?php if ($group_row->form_field_category_id == $category_row->form_field_category_id): ?>
		<tr>
			<td class="font-weight-bold">
				&nbsp;&nbsp;	
				<?=$group_row->form_field_category_sequence .'.' . $group_row->form_field_group_sequence . ' ' . $group_row->form_field_group_name ?>
				<small><?=(empty($group_row->form_field_group_description)) ? '' : '(' . ucwords($group_row->form_field_group_description) . ')' ?></small>
			</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<?php foreach ($fields as $field_row): ?>
			<?php if ($group_row->form_field_group_id == $field_row->form_field_group_id): 
					$field_label = $field_row->form_field_label;
				?>
				<tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	
						<?= $field_label ?>
						<small><?=(empty($field_row->form_field_description)) ? '' : '(' . ucwords($field_row->form_field_description) . ')' ?></small>
					</td>
					<?php $answer_color = ($field_row->is_standard == '0') ? 'text-danger' : ''?>
					<td class="text-center <?=$answer_color?>"><?= $field_row->form_header_detail_field_answer ?></td>
					<td class="text-center"><?=$field_row->standard_rule_label?></td>
					<td class="text-center">
						<p><?=$field_row->remarks?></p>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endforeach; ?>
	</tbody>
</table>
<style>
	.table td, .table th {
		padding: 0.6rem;
	}
	.table-bordered td, .table-bordered th {
		border: 1px solid #dee2e6;
	}
	.table td, .table th {
		padding: 0.75rem;
		vertical-align: top;
		border-top: 1px solid #dee2e6;
	}
	.font-weight-bold {
		font-weight: 700;
	}
	.text-center {
		text-align: center;
	}
	.text-danger {
		color: #dc3545;
	}
	.table {
		font-size: 9px;
	}
</style>