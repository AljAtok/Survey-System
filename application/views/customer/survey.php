<form  id="survey-form" class="needs-validation" data-csrf-name="<?= $this->security->get_csrf_token_name(); ?>" data-csrf-hash="<?= $this->security->get_csrf_hash(); ?>" >

    <div class="d-flex banner-bg">

        <div class="survey-container m-auto px-1">

            <section class="upper-content-container">
                <?= $survey_content ?>
            </section>

            <section class="lower-content-container">

                <div class="bg-light rounded py-2 px-3">
                    <div class="row">
                        <div class="<?= isset($stores) ? 'col-sm-12 col-md-4 position-relative' : 'col-12 position-relative' ?>">
                            <label class="font-avenir" for="or_number">OR Number</label>
                            <input class="form-control shadow-none" name="<?= encode('or_number') ?>" type="text" value="" required>
                        </div>
                        <?php if (isset($stores)): ?>
                            <div class="col-sm-12 col-md-8">
                                <label for="store" class="font-avenir">Store</label>
                                <select id="store" type="text" class="form-control shadow-none select2" name="<?= encode('store') ?>" required
                                
                                data-select2-config='{
                                    "placeholder": "",
                                    "allowClear": true
                                }'
                                
                                >
                                    <!-- <option value="" hidden disabled selected></option> -->
                                    <option></option>
                                    <?php foreach ($stores as $store) : ?>
                                        <option value="<?= $store->store_id ?>"><?= $store->store_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="my-3 p-3 border rounded bg-light">
                    <p class="m-0">Disclaimer: This survey is intended solely for the enhancement of our products and services. All information gathered is safeguarded under the laws and regulations pertaining to data privacy in the Philippines and other relevant jurisdictions.</p>
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