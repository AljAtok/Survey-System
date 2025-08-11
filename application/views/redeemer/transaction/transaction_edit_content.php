<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<hr>
<h3><?=$title?></h3>
<hr>

<?=$this->session->flashdata('message');?>

<div class="container-fluid" >

	<div id="stepperForm" class="bs-stepper">

		<?php 
		$transaction_id = '';
		$section_name = '';
		if(isset($transaction)){ 
			$transaction_id .= '<input type="hidden" name="transaction_id" value="' . $transaction->transaction_id . '">';
			$process = base_url($controller.'/update-transaction');
		} else {
			$process = base_url($controller.'/store-transaction');
		}
		
		?>

		<form method="POST" action="<?= $process ?>" class="needs-validation" enctype="multipart/form-data" id="transaction-form" novalidate>
			<?= $transaction_id ?>

			<div class="bs-stepper-content">
				<hr>

				<input type="hidden" value="<?= encode($form_id) ?>" name="form_id" required>
				<input type="hidden" value="<?= $expiration ?>" name="expiration" required>

					<?php foreach($sections as $section_row): ?>
						<fieldset>
							<legend><?= $section_row->section_name ?></legend>
						
							<?php 
								$i = 0;
								foreach($fields as $field):
								$i++;
								if($field->section_id == $section_row->section_id): 
									$field_name = str_replace(' ', '', strtolower($field->form_field_name)) . $i;
									?>
								
									<?php switch($field->field_type_id): 
										case 1: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="w-100">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<div class="mt-2">
														<textarea class="w-75" id="<?= $field_name ?>" name="<?= $field_name ?>"><?php if(isset($field->response)){ echo $field->response; } ?></textarea>
													</div>
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>" value="<?php if(isset($field->response_id)){ echo $field->response_id; }else{ echo $field->field_id; }?> ?>">
											</div>
										<?php break; ?>
										<?php case 2: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<div class="mt-2">
														<input id="<?= $field_name ?>" name="<?= $field_name ?>" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
													</div>
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>" value="<?php if(isset($field->response_id)){ echo $field->response_id; }else{ echo $field->field_id; }?> ?>">
											</div>
										<?php break; ?>
										<?php case 3: ?>
											<div class="my-2">
												<div class="d-flex justify-content-between">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<input id="<?= $field_name ?>" name="<?= $field_name ?>" type="number" value="<?php if(isset($field->response)){ echo $field->response; } ?>" min="0" inputmode="numeric">
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="field_id[]" value="<?= $field->field_id ?>">
												<?php if(isset($field->response)) : ?>
													<input type="hidden" name="response_id[]" value="<?= $field->response_id ?>">
												<?php endif; ?>
												<input type="hidden" name="is_number[]" value="<?= $field->field_id ?>">
											</div>
										<?php break; ?>
										<?php case 4: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="">
													<label for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
													
													<?php foreach($options as $option) : 
														if($option->field_id == $field->field_id) : ?>
														<div class="d-flex align-items-center my-1">
															<input class="mr-1" id="<?=$field_name?>" name="<?=$field_name?>" value="<?=$option->option_name?>" type="radio" <?php if(isset($field->response_id) && $option->option_name == $field->response) echo 'checked' ?>>
															<?= $option->option_name ?>
														</div>
													<?php endif; endforeach; ?>
												</div>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="field_id[]" value="<?= $field->field_id ?>">
											</div>
											<hr>
										<?php break; ?>
										<?php case 5: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="">
													<label for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
													<select name="<?=$field_name?>">
														<option selected hidden disabled>Select</option>
														<?php foreach($options as $option) : 
															if($option->field_id == $field->field_id) : ?>
															<option value="<?= $option->option_name ?>" <?php if(isset($field->response_id) && $option->option_name == $field->response) echo 'selected' ?>>
																<?= $option->option_name ?>
															</option>
														<?php endif; endforeach; ?>

													</select>
												</div>
												
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="field_id[]" value="<?= $field->field_id ?>">
											</div>
											<hr>
										<?php break; ?>
										<?php case 6: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="">
												<label for="" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
													<?php $i = 0; foreach($options as $option) : 
														$i++;
														if($option->field_id == $field->field_id) : ?>

														<div class="d-flex align-items-center my-1">
															<input class="mr-1" id="" name="<?= $field_name ?>-<?= $i ?>" value="<?=$option->option_name?>" type="checkbox">
															<?= $option->option_name ?>
														</div>

														<input type="hidden" name="field_name[]" value="<?= $field_name ?>-<?= $i ?>">
														<input type="hidden" name="field_id[]" value="<?= $field->field_id ?>">

													<?php endif; endforeach; ?>
												</div>
											</div>
											<hr>
										<?php break; ?>
										<?php case 7: ?>
											<div class="d-flex justify-content-between my-2">
												<div class="">
													<label for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
													
													<div class="d-flex justify-content-between align-items-center my-2">
														<?php $first_iteration = true;
															foreach($options as $option) : 
																if($option->field_id == $field->field_id) : ?>
																	<p class="m-0"><?= $option->option_name ?></p>
																	<?php if($first_iteration) 

																		echo   '
																		<div class="d-flex flex-column text-center">
																			1
																			<input type="radio" name="'.$field_name.'" value="1">
																		</div>
																		<div class="d-flex flex-column text-center">
																			2
																			<input type="radio" name="'.$field_name.'" value="2">
																		</div>
																		<div class="d-flex flex-column text-center">
																			3
																			<input type="radio" name="'.$field_name.'" value="3">
																		</div>
																		<div class="d-flex flex-column text-center">
																			4
																			<input type="radio" name="'.$field_name.'" value="4">
																		</div>
																		<div class="d-flex flex-column text-center">
																			5
																			<input type="radio" name="'.$field_name.'" value="5">
																		</div>';
																		$first_iteration = false;
																	?>
														<?php endif; endforeach; ?>
													</div>
													
												</div>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="field_id[]" value="<?= $field->field_id ?>">
											</div>
											<hr>
										<?php break; ?>
										<?php case 8: ?>
											<div class="my-2">
												<div class="d-flex justify-content-between">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
													<input id="<?= $field_name ?>" name="<?= $field_name ?>" type="file" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>" value="<?php if(isset($field->response_id)){ echo $field->response_id; }else{ echo $field->field_id; }?> ?>">
											</div>
										<?php break; ?>
										<?php case 9: ?>
											<div class="my-2">
												<div class="d-flex justify-content-between">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<input id="<?= $field_name ?>" name="<?= $field_name ?>" type="date" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>" value="<?php if(isset($field->response_id)){ echo $field->response_id; }else{ echo $field->field_id; }?> ?>">
											</div>
										<?php break; ?>
										<?php case 10: ?>
											<div class="my-2">
												<div class="d-flex justify-content-between">
													<label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
													<input id="<?= $field_name ?>" name="<?= $field_name ?>" type="time" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
												</div>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>" value="<?php if(isset($field->response_id)){ echo $field->response_id; }else{ echo $field->field_id; }?> ?>">
											</div>
										<?php break; ?>
									<?php endswitch; ?>
								<?php endif; ?>
							<?php endforeach; ?>
							<hr>
						</fieldset>					
					<?php endforeach; ?>
			</div>
				
			<button type="submit" class="btn btn-sm btn-primary float-right" id="submit-btn">Save</button>

			
		</form>
	</div>
</div>


