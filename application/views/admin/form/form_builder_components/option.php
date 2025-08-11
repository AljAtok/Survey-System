<div class="form-option mt-1" data-option-id="<?= $option_data->option_id ?>">
    <div class="row">
        <div class="col-1"></div>
        <div class="col-1 d-flex justify-content-center">
            <label class="d-none" for="sequence"></label>
            <input type="numerical" class="form-control form-control-sm w-100 border-0 border-bottom text-center"  name="option_sequence" placeholder="" value="<?= $option_data->option_sequence ?>" required>
        </div>
        <div class="col-6">
            <label class="d-none" for="option_name"></label>
            <input type="text" class="form-control form-control-sm border-0 rounded-0 border-bottom border-dark-subtle"  name="option_name" placeholder="Option Title" value="<?= $option_data->option_name ?>" required>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-sm btn-danger delete-option">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>
</div>