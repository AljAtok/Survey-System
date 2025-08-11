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

<div id="ctgreports" data-value="<?=encode(2)?>">

<div class="filter-container">
        <form class="container-fluid" method="post" action="<?= base_url( 'admin/survey_export/' . encode(2) )?>" target="_blank">
            <div class="row">

                <div class="col-sm-12 col-md-6">
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

                <div class="col-sm-12 col-md-6">
                    <button class="btn btn-success btn-sm w-100" type="submit"> Export <i class="fa fa-file-excel-o"></i> </button>
                </div>

            </div>
        </form>
    </div>

    <hr>

    <div class="report-container">

        <div class="table-responsive">

            <table class="table table-striped mb-5 mt-3 w-100 ctg-data-table" data-url="<?= base_url('admin/load_report_page_table/'. encode(2)) ?>">
                <thead class="table-dark">
                    <tr>
                        <th>Reference Number</th>
                        <th>OR Number</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>

</div>