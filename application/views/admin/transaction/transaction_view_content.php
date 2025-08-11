<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<div class="d-flex justify-content-between">
	<h3><?=$title?></h3> 
</div>
	
<hr>
<?=$this->session->flashdata('message');?>

<div class="container-fluid" >
	<div id="stepperForm" class="bs-stepper">
		<div class="bs-stepper-content">
			<hr>
			<?php foreach($sections as $section_row): ?>
				
				<fieldset>
				<legend><?= $section_row->section_name ?></legend>
				
				
					<?php foreach($fields as $field): ?>
						<?php if($field->section_id == $section_row->section_id && $field->field_type_id != 8): 
							$shorten_field_name = str_replace(' ', '', strtolower($field->field_name));
							?>
							<?php switch($field->field_type_id): 
								case 1: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
								<?php break; ?>
								<?php case 2: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
								<?php break; ?>
								<?php case 3: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
								<?php break; ?>
								<?php case 4: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
									
								<?php break; ?>
								<?php case 5: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
						
								<?php break; ?>
								<?php case 6: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<?php $checkbox_response = json_decode(decode($field->response))?>	
										<?php foreach($checkbox_response as $response): ?>
											<p class="border my-1 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= $response ?></p>
										<?php endforeach; ?>
									</div>
								<?php break; ?>
								<?php case 7: ?>
									<div class="d-flex justify-content-between flex-column my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
									
								<?php break; ?>
								<?php case 8: ?>
									404
								<?php break; ?>
								<?php case 9: ?>
									<div class="d-flex justify-content-between my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>" name="<?= $shorten_field_name ?>" ><?= decode($field->response) ?></p>
									</div>
								<?php break; ?>
								<?php case 10: ?>
									<div class="d-flex justify-content-between my-2">
										<label for="<?= $shorten_field_name ?>" ><?= $field->field_name ?></label>
										<p class="border m-0 p-1 px-3 rounded w-fit-content" id="<?= $shorten_field_name ?>" name="<?= $shorten_field_name ?>"><?= decode($field->response) ?></p>
									</div>
								<?php break; ?>
							<?php endswitch; ?>
						<?php endif; ?>
					<?php endforeach; ?>
					
				</fieldset>					
			<?php endforeach; ?>
		</div>
	</div>
</div>