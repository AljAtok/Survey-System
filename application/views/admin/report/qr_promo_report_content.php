<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
    </ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>
<?=$this->session->flashdata('message');?>

<div id="cdireports">

    <div class="filter-container">
        <form class="container-fluid" method="post" action="<?= base_url( 'admin/survey_export/' . encode($form_id) )?>" target="_blank">
            <div class="row">

                <div class="col-sm-12 col-md-4">
                    <div class="row">
                        <label for="date-filter" class="col-sm-4 col-form-label col-form-label-md">
                            Date Filter:
                        </label>
                        <div class="col-sm-8">
                            <input name="date-filter" class="datepicker form-control form-control-sm"
                            data-picker-config='{
                                "mode": "range",
                                "dateFormat": "Y-m-d"
                            }'>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="row">
                        <label for="province-filter" class="col-sm-2 col-form-label col-form-label-md">Province:</label>
                        <div class="col-sm-10">
                            <select class="form-select form-select-sm shadow-none select2" name="province-filter"
							data-select2-config='{
									"placeholder": "Select Province",
									"allowClear": true
								}'
							>
                                <option value="" selected>Show All</option>
                                <?php foreach($provinces as $province): ?>
                                    <option value="<?= $province->province_id ?>"><?= $province->province_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <button class="btn btn-success btn-sm w-100" type="submit"> Export <i class="fa fa-file-excel-o"></i> </button>
                </div>

            </div>
        </form>
    </div>

    <hr>

    <div class="report-container">

        <div class="table-responsive">

            <table class="table table-striped mb-5 mt-3 w-100 qr-promo-data-table" data-url="<?= base_url('admin/load_report_page_table_for_qr_promo/'. encode($form_id)) ?>">
                <thead class="table-dark">
                    <tr>
                        <th>Reference Number</th>
                        <th>Province</th>
                        <th>Brgy. & Town</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Is Winner</th>
                        <th>Winner Date</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
</div>