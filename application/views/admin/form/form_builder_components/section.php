<section class="form-section" data-section-id="<?= $section_data->section_id ?>">

    <div class="section-upper-content p-3 shadow bg-light rounded mt-2">
        <div class="row">
            <div class="col-1">
                <label class="d-none" for="sequence"></label>
                <input type="numerical" class="form-control w-100 border-0 border-bottom text-center"  name="section_sequence" placeholder="" value="<?= $section_data->section_sequence?>">
            </div>
            <div class="col-7">
                <label class="d-none" for="section_name"></label>
                <input type="text" class="form-control border-0 border-bottom"  name="section_name"  placeholder="Section Title" value="<?= $section_data->section_name ?>">
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-sm btn-primary w-100 add-field"> 
                    <span class="fas fa-plus-circle"></span> Add Field
                </button>
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-sm btn-danger delete-section">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-secondary toggle-section">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="section-inner-content">
        <br>
        <div class="field-container">
            <?= $fields ?>
        </div>
    </div>

</section>

