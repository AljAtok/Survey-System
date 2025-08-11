<?=$this->session->flashdata('message');?>

	<form method="POST" action="<?= base_url($controller.'/validate-transaction') ?>" class="needs-validation" enctype="multipart/form-data" id="transaction-form" novalidate>
	
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

		<div class="d-flex banner-bg">

			<!-- <div class="position-relative">
				<img class="position-absolute" style="left:0.5rem; top:1rem;" src="<?= base_url('assets\img\chooks-icon.png') ?>"> 
			</div> -->

			<div class="survey-container m-auto py-5 px-3">
			
				

				<?php foreach($sections as $section_row): ?>

					<fieldset>
						<legend class="text-center mt-2 p-3 border rounded bg-light font-chunkfive"><?= $section_row->section_name ?></legend>
					
						<?php
							foreach($fields as $field):
							if($field->section_id == $section_row->section_id): 
								
								$field_name = str_replace(' ', '', strtolower($field->form_field_name));
								?>
							
								<?php switch($field->field_type_id): 
									case 1: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="w-100">
												<label class="m-0 <?php echo $field->is_required == 1 ? 'required' : ''; ?>" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 100%;"><?= $field->form_field_description ?></p>
												<div class="mt-2">
													<textarea id="<?= $field_name ?>" class="form-control" name="<?= $field_name ?>" type="text" <?php echo $field->is_required == 1 ? 'required' : ''; ?>><?= $this->session->flashdata('form_data')[$field_name] ?? '' ?></textarea>
												</div>
											</div>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
											
										</div>
									<?php break; ?>
									<?php case 2: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="">
												<label class="m-0 <?php echo $field->is_required == 1 ? 'required' : ''; ?>" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 100%;"><?= $field->form_field_description ?></p>
												<div class="mt-2">
													<input id="<?= $field_name ?>" class="form-control" name="<?= $field_name ?>" type="text" value="<?= $this->session->flashdata('form_data')[$field_name] ?? '' ?>" <?php echo $field->is_required == 1 ? 'required' : ''; ?>>
												</div>
												<?php if(isset($data)){ echo '<small>invalid email</small>'; } ?>
												
											</div>
											
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>
									<?php break; ?>
									<?php case 3: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="">
												<label class="m-0 <?php echo $field->is_required == 1 ? 'required' : ''; ?>" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<div class="mt-2">
													<input id="<?= $field_name ?>" class="form-control" name="<?= $field_name ?>" type="number" min="0" value="<?= $this->session->flashdata('form_data')[$field_name] ?? '' ?>" <?php echo $field->is_required == 1 ? 'required' : ''; ?>>
												</div>
											</div>
											<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>
									<?php break; ?>
									<?php case 4: ?> 
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="">
												<label for="<?= $field_name ?>" class="<?php echo $field->is_required == 1 ? 'required' : ''; ?>"><?= $field->form_field_name ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 100%;"><?= $field->form_field_description ?></p>
												
												<?php foreach($options as $option) : 
													if($option->field_id == $field->field_id) : ?>
													<div class="form-check d-flex align-items-center my-1">
														<input class="form-check-input" style="" id="<?=$field_name?>" name="<?=$field_name?>" value="<?=encode($option->option_id)?>" type="radio" <?= (isset($this->session->flashdata('form_data')[$field_name]) && decode($this->session->flashdata('form_data')[$field_name]) == $option->option_id) ? 'checked' : '' ?> <?php echo $field->is_required == 1 ? 'required' : ''; ?>>
														<?= $option->option_name ?>
													</div>
												<?php endif; endforeach; ?>
											</div>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>							
									<?php break; ?>
									<?php case 5: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="">
												<label for="<?= $field_name ?>" class="<?php echo $field->is_required == 1 ? 'required' : ''; ?>" ><?= $field->form_field_name ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 100%;"><?= $field->form_field_description ?></p>
												<select name="<?=$field_name?>" class="form-control" <?php echo $field->is_required == 1 ? 'required' : ''; ?>>
												
													<?php if(!$this->session->flashdata('form_data')[$field_name]) : ?>
														<option selected hidden disabled value="">Select</option>
													<?php endif; ?>

													<?php foreach($options as $option) : 

														if($option->field_id == $field->field_id) : ?>
														<option value="<?= encode($option->option_id) ?>" <?= (isset($this->session->flashdata('form_data')[$field_name]) && decode($this->session->flashdata('form_data')[$field_name]) == $option->option_id) ? 'selected' : '' ?>>
															<?= $option->option_name ?>
														</option>

													<?php endif; endforeach; ?>
												</select>
											</div>									
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>								
									<?php break; ?>
									<?php case 6: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="">
											<label for="" class="<?php echo $field->is_required == 1 ? 'required' : ''; ?>" ><?= $field->form_field_name ?></label>
											<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 100%;"><?= $field->form_field_description ?></p>
												<?php foreach($options as $option) : 
													if($option->field_id == $field->field_id) : ?>

													<div class="d-flex align-items-center my-1">
														<input class="mr-1" id="" name="<?= $field_name ?>[]" value="<?=encode($option->option_id)?>" type="checkbox" <?= isset($this->session->flashdata('form_data')[$field_name]) == $option->option_name ? 'checked' : '' ?>>
														<?= $option->option_name ?>
													</div>

													<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
												<?php endif; endforeach; ?>
											</div>
										</div>
									<?php break; ?>
									<?php case 7: ?>
										<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
											<div class="w-100">
												<label for="<?= $field_name ?>" class="<?php echo $field->is_required == 1 ? 'required' : ''; ?>"><?= $field->form_field_name ?></label>
												<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
												
												<div class="d-flex justify-content-between align-items-center my-2">
													<?php $first_iteration = true;
														foreach($options as $option) : 
															if($option->field_id == $field->field_id) : ?>
																<p class="m-0"><?= $option->option_name ?></p>
																<?php 
																if($first_iteration) {
																	for($i = 1; $i <= 5; $i++){
																		echo '<div class="d-flex flex-column text-center">
																			'.$i.'
																			<input type="radio" name="'.$field_name.'" value="'.encode($i).'" '. (isset($this->session->flashdata('form_data')[$field_name]) && decode($this->session->flashdata('form_data')[$field_name]) == $i ? 'checked' : '') . '> 
																		</div>';
																	}
																	$first_iteration = false;
																}
																?>
													<?php endif; endforeach; ?>
												</div>
												<!-- ($field->is_required == 1) ?? 'required' : '' ). -->
											</div>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>							
									<?php break; ?>
									<?php case 8: ?>
										<div class="my-2 p-3 border rounded bg-light">
											<div class="d-flex justify-content-between">
												404
												<!-- <label class="m-0" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<input id="<?= $field_name ?>" name="<?= $field_name ?>" type="file"> -->
											</div>
											<!-- <p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
											<input type="hidden" name="<?php if(isset($field->response_id)){ echo 'response_id[]'; }else{ echo 'field_id[]'; }?>"?>  -->
										</div>
									<?php break; ?>
									<?php case 9: ?>
										<div class="my-2 p-3 border rounded bg-light">
											<div class="d-flex justify-content-between">
												<label class="m-0 <?php echo $field->is_required == 1 ? 'required' : ''; ?>" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<input id="<?= $field_name ?>" class="form-control" name="<?= $field_name ?>" type="date" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
											</div>
											<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>
									<?php break; ?>
									<?php case 10: ?>
										<div class="my-2 p-3 border rounded bg-light">
											<div class="d-flex justify-content-between">
												<label class="m-0 <?php echo $field->is_required == 1 ? 'required' : ''; ?>" for="<?= $field_name ?>" ><?= $field->form_field_name ?></label>
												<input id="<?= $field_name ?>" class="form-control" name="<?= $field_name ?>" type="time" value="<?php if(isset($field->response)){ echo $field->response; } ?>">
											</div>
											<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
											<input type="hidden" name="field_name[]" value="<?= $field_name ?>">
										</div>
									<?php break; ?>
								<?php endswitch; ?>
							<?php endif; ?>
						<?php endforeach; ?>

					</fieldset>					
				<?php endforeach; ?>

				<div class="d-flex justify-content-between my-2 p-3 border rounded bg-light">
					<div class="<?php echo isset($stores) ? 'mr-1 w-50' : 'w-100'; ?>">
						<label class="m-0 required" for="or_number" >OR Number</label>
						<input id="" class="form-control" name="or_number" type="text" value="<?= $this->session->flashdata('form_data')['or_number'] ?? '' ?>" required>
					</div>
					<?php 
						if(isset($stores)){

						$flashData = $this->session->flashdata('form_data');
						
						$selectedStore = $flashData ? decode($flashData['store']) : '';

						$select_store =		'<div class="w-100">
												<label class="m-0 required" for="or_number" >Store</label>
												<select id="" class="form-control" name="store" required>
													<option selected hidden disabled value="">Select</option>';
													foreach($stores as $store){
						$select_store .= 				'<option value="' . encode($store->store_id) . '" ' . $selectedStore == $store->store_id ? 'selected' : '' . '>' . $store->store_name . '</option>';
													}
						$select_store .=		'</select>
											</div>';
													
						echo $select_store;
						}
						
					?>
				</div>

				<div class="my-3 p-3 border rounded bg-light">
				Disclaimer: This survey is intended solely for the enhancement of our products and services. All information gathered is safeguarded under the laws and regulations pertaining to data privacy in the Philippines and other relevant jurisdictions.
				</div>
				
				<div class="d-flex flex-column align-items-center border rounded bg-light py-3">		
						<?= $captcha['image']; ?>
						<input type="text" class="mt-2 w-50 form-control" id="captcha" name="captcha" placeholder="Enter the captcha" required>
					
					
					<!-- <button class="btn btn-sm btn-light" type="reset" >Clear Form</button> -->
				</div>
				<div class="d-flex justify-content-center mt-3">				
					<button type="submit" class="btn btn-sm btn-light w-50" style="font-size:larger" id="submit-btn">Submit</button>
				</div>

			</div>

		</div>
	</form>


