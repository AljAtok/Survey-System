<form  id="survey-form" class="needs-validation" data-csrf-name="<?= $this->security->get_csrf_token_name(); ?>" data-csrf-hash="<?= $this->security->get_csrf_hash(); ?>" >

    <div class="d-flex mosaic-bg">

        <div class="survey-container m-auto px-1">

            <section class="upper-content-container">
                <?= $survey_content ?>
            </section>

            
            <section class="lower-content-container">
				<?php if (isset($form_id) && $form_id == 5): ?>
					<div class="mt-0 mb-2 bg-light rounded py-2 px-3">
						<div class="row">
							<div class="col-12 position-relative">
								<label class="font-avenir" for="or_number">Receipt Number</label>
								<input class="form-control border-0 border-bottom border-dark rounded-0 shadow-none" name="<?= encode('or_number') ?>" type="text" value="" placeholder="Order Number on the Receipt" required>
							</div>
						</div>
					</div>
					<div class="mt-0 mb-2 bg-light rounded py-2 px-3">
						<!-- <div class="row">
							<div class="col-12 position-relative">
								<label class="font-avenir" for="or_photo">Receipt Photo</label><br>
								<small class="form-text text-muted">Upload a clear photo of your receipt (JPG or PNG only, MAX 5MB).</small><br>
								<small class="form-text text-muted">Sales Date and Order Number should be visible.</small>
								<input class="form-control" name="or_photo" type="file" accept=".jpg,.jpeg,.png" required>
								<div id="or-photo-preview" class="mt-2 text-center"></div>
								
							</div>
						</div> -->
						<div class="mt-3">
							<div class="col-12 position-relative">
								<label class="font-avenir" for="or_photo">Receipt Photo</label><br>
								<small class="form-text text-muted">Capture a clear photo of your receipt (Max Reso: 5MB).</small><br>
								<small class="form-text text-muted d-inline-block mb-2" style="vertical-align: middle;">Sales Date and Order Number should be visible.</small>
								<span class="d-inline-block ms-2 mb-1" style="vertical-align: middle;">
									<a href="#" id="sample-receipt-link" style="text-decoration: none; color: #007bff; cursor: pointer;" tabindex="0">
										<i class="fa fa-image"></i> View Sample Receipt Photo
									</a>
								</span>
								<div id="sample-receipt-preview" style="display:none; position: absolute; z-index: 10; background: #fff; border: 1px solid #ccc; padding: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
									<img src="<?= base_url('assets/img/Sample-CTG-Reciept.png') ?>" alt="Sample Receipt" style="max-width: 250px; max-height: 350px; border-radius: 6px;">
								</div>
								
							</div>
							<button type="button" class="btn btn-primary" id="capture-photo-btn">
								Launch Camera
							</button>
							<div class="d-flex justify-content-center">
								<video id="camera-stream" width="320" height="100%" autoplay style="display:none; border-radius:8px; margin-top:10px; object-fit:cover; background:#eee; aspect-ratio:3/4;"></video>
								<canvas id="camera-canvas" width="320" height="100%" style="display:none; border-radius:8px; margin-top:10px; background:#eee; aspect-ratio:3/4;"></canvas>
							</div>
							<div class="d-flex justify-content-center gap-2">
								<button type="button" class="btn btn-success mt-2" id="take-photo-btn" style="display:none;">Take Photo</button>
								<button type="button" class="btn btn-secondary mt-2" id="close-camera-btn" style="display:none;">Close Camera</button>
							</div>
							<div id="captured-photo-preview" class="mt-2 text-center"></div>
							<input type="hidden" name="or_photo" id="or_photo_camera">
						</div>
					</div>
				<?php endif; ?>

				<div class="bg-light rounded py-2 px-3">
                    <div class="row">
						
                        <div class="col-sm-12 col-md-4 position-relative">
							<?php if (isset($provinces)): ?>
								<label for="province" class="font-avenir">Province</label>
								<select id="province" type="text" class="form-control shadow-none select2" name="<?= encode('province') ?>" required
								
								data-select2-config='{
									"placeholder": "Select Province",
									"allowClear": true
								}'
								
								>
									<!-- <option value="" hidden disabled selected></option> -->
									<option></option>
									<?php foreach ($provinces as $province) : ?>
										<option value="<?= encode($province->province_id) ?>"><?= $province->province_name ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
                        </div>
                        
						<div class="col-sm-12 col-md-4">
							<label for="town" class="font-avenir">Town/City</label>
							<select id="town" type="text" class="form-control shadow-none select2" name="<?= encode('town') ?>" required
							
							data-select2-config='{
								"placeholder": "Select Province First",
								"allowClear": true
							}'
							
							>
								<!-- <option value="" hidden disabled selected></option> -->
								
								
							</select>
						</div>
						<div class="col-sm-12 col-md-4">
							<label for="brgy" class="font-avenir">Barangay</label>
							<select id="brgy" type="text" class="form-control shadow-none select2" name="<?= encode('brgy') ?>" required
							
							data-select2-config='{
								"placeholder": "Select Town/City First",
								"allowClear": true
							}'
							
							>
								<!-- <option value="" hidden disabled selected></option> -->
								
								
							</select>
						</div>
                    </div>
                </div>
                


                <div class="mt-3 mb-2 p-3 border rounded bg-light">
                    <!-- <p class="m-0">Disclaimer: This survey is intended solely for the enhancement of our products and services. All information gathered is safeguarded under the laws and regulations pertaining to data privacy in the Philippines and other relevant jurisdictions.</p> -->
                    <p class="m-0"><strong>Reminder:</strong><br>
					1. Please register a valid email address and contact number. This is our only way to reach you in case you win. Name submitted in the survey should match the name of the valid ID that will be presented during redemption of freebies.<br>
					2. You may only win once during the promo period to give chance for others to participate.
					</p>
					<p class="m-0">
						<strong>**Data Privacy Clause**</strong><br>
						Your data is safe with us! Exclusively used by <?=$company_name?> for promotions. Your privacy matters to us. All information gathered by <?=$company_name?> for promotion is safeguarded under the laws and regulations pertaining to data privacy in the Philippines and other relevant jurisdictions. Your privacy matters to us.
					</p>
					
                    <!-- <p class="m-0"><strong>Disclaimer</strong>: All information gathered is safeguarded under the laws and regulations pertaining to data privacy in the Philippines and other relevant jurisdictions.</p> -->
                </div>
                
				<div class="mb-2 p-3 border rounded bg-light">
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="agree_checkbox" value="true" class="custom-control-input" id="agreeChck" required>
								<label class="custom-control-label" for="agreeChck">I agree to the <a href="https://reseller.chookstogo.com.ph/customer/terms-of-use-and-privacy-policy" target="_blank">Terms of Use</a> and <a href="https://reseller.chookstogo.com.ph/customer/terms-of-use-and-privacy-policy#privacy_policy" target="_blank">Privacy Policy</a></label>
							<div class="invalid-feedback">
								You must agree before submitting.
							</div>
						</div>
					</div>
                </div>
                
                <div id="captcha-container" class="d-flex flex-column align-items-center border rounded bg-light py-3">		
                    <?= $captcha['image']; ?>
                    <input type="text" class="mt-2 w-50 form-control" name="<?= encode('captcha') ?>" placeholder="Enter the captcha" required>
                </div>
                <div class="d-flex justify-content-center my-3">				
                    <button type="button" class="btn btn-sm btn-light w-50" style="font-size:larger" id="submit-btn">Submit</button>
                </div>
                
            </section>

        </div>

    </div>

</form>
