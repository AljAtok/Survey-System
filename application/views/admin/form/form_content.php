<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item text-muted">File Maintenance</li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>
<?=$this->session->flashdata('message');?>
<div class="table-container">

	<?php if($employee_permission['create_form']):?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4">
					<button type="button" class="btn btn-sm btn-primary create-form">
						<span class="fas fa-plus-circle"></span> Create New Form
					</button>
				</div>
			</div>
		</div>
		<hr>
	<?php endif;?>

	<div class="table-responsive">
		<table class="table table-striped mb-5 mt-3 w-100 data-table">
			<thead class="table-dark">
				<tr>
					<th scope="col">Form Name</th>

					<th scope="col">Status</th>
					<th scope="col" class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($forms as $form): 
						$encoded_form_id = encode($form->form_id);

						$disabled = $employee_permission['update_form_content'] ? '' : 'disabled';
						
						$action = '
						<div class="d-flex justify-content-around align-items-center">
							<a class="btn btn-add btn-sm btn-primary text-white '. $disabled .'" href="'.base_url('admin/form-content/'.$encoded_form_id).'">Edit</a>
						';
						if($form->status == 1){
							$badge  = '<span class="badge text-bg-success">Active</span>';
							$action .= '<a href="" class="toggle-active text-success" data-id="' . encode($form->form_id) . '"><span class="fas fa-toggle-on fa-lg"></span></a></div>';
						}elseif($form->status == 0){
							$badge  = '<span class="badge text-bg-warning">Inactive</span>';
							$action .= '<a href="#" class="toggle-inactive text-warning" data-id="' . encode($form->form_id) . '"><span class="fas fa-toggle-off fa-lg"></span></a></div>';
						}
					?>
					<tr>
						<td><?= $form->form_name ?></td>

						<td><?= $badge ?></td>
						<td><?= $action ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>

<!-- Create Form -->
<!-- <div class="modal fade" id="modal-add-form" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel"><strong>Add Form</strong></h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form method="POST" action="<?=base_url($controller.'/store-form')?>" class="needs-validation" novalidate>
				<div class="modal-body">
					<div class="form-group">
						<label for="form_name">Form Name: </label>
						<input type="text" id="form_name" name="form_name" class="form-control form-control-sm" value="" required>
						<div class="invalid-feedback">Form Name is required.</div>
					</div>
				</div>
			
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary btn-main btn-sm" id="submit-btn">Save</button>
				</div>
			</form>

		</div>
	</div>
</div> -->

<!-- Edit Form -->
<!-- <div class="modal fade" id="modal-edit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel"><strong>Update Form</strong></h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form method="POST" action="<?=base_url($controller.'/update-form')?>" id="update-form" class="needs-validation" novalidate>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary btn-main btn-sm">Update</button>
				</div>
			</form>

		</div>
	</div>
</div> -->

<!-- Activate Toggle -->
<!-- <div class="modal fade" id="modal-active" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel"><strong>Activate Form</strong></h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="<?=base_url($controller.'/activate-form')?>" id="activate-form">
				<div class="modal-body">
					<input type="hidden" name="id" id="id">
					<p class="text-center"><strong>Are you sure to activate this form?</strong></p>

					<p class="text-center">
						<button type="submit" class="btn btn-sm btn-success btn-yes">Yes</button>&nbsp;
						<button type="button" class="btn btn-danger btn-sm btn-no" data-dismiss="modal">No</button>
					</p>
				</div>
			</form>
		</div>
	</div>
</div> -->

<!-- Deactivate Toggle -->
<!-- <div class="modal fade" id="modal-deactivate" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel"><strong>Deactivate Form</strong></h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="<?=base_url($controller.'/deactivate-form')?>" id="deactivate-form">
				<div class="modal-body">
					<input type="hidden" name="id" id="id">
					<p class="text-center"><strong>Are you sure to deactivate this form?</strong></p>

					<p class="text-center">
						<button type="submit" class="btn btn-sm btn-success btn-yes">Yes</button>&nbsp;
						<button type="button" class="btn btn-danger btn-sm btn-no" data-dismiss="modal">No</button>
					</p>
				</div>
			</form>
		</div>
	</div>
</div> -->

