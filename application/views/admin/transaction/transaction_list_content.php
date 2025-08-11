<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>

<select class="select2 w-25" id="form-select"
    data-select2-config='{
        "dropdownAutoWidth": true
    }'>
    <?php foreach($forms as $form): ?>
        <option value="<?= encode($form->form_id) ?>"><?= $form->form_name ?></option>
    <?php endforeach; ?>
</select>
<br>

<div class="table-responsive">
    <table id="dynamic-table" class="table table-striped w-100" data-url="">
        <thead class="table-dark">
            <tr>

            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>




<!-- 
<nav>
  <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
    <a href="#cdi-tab" class="nav-link active" id="nav-cdi-tab" data-bs-toggle="tab" data-bs-target="#nav-cdi" type="button" role="tab" aria-controls="nav-cdi" aria-selected="true">CDI Survey</a>
    <a href="#ctg-tab" class="nav-link" id="nav-ctg-tab" data-bs-toggle="tab" data-bs-target="#nav-ctg" type="button" role="tab" aria-controls="nav-ctg" aria-selected="false">CTG Survey</a>
  </div>
</nav>
-->

<!-- 
<div class="tab-content" id="nav-tabContent">

    <div class="tab-pane fade show active p-2" id="nav-cdi" role="tabpanel" aria-labelledby="nav-cdi-tab" tabindex="0">
        <div class="table-responsive">
            <table id="cdi-table" class="table table-striped w-100" data-url="<?= base_url('admin/load_transaction_page_table/'. encode(1)) ?>">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Reference #</th>
                        <th scope="col">OR #</th>
                        <th scope="col">Store</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact #</th>
                        <th scope="col">Email</th>
                        <th scope="col">Date Added</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade p-2" id="nav-ctg" role="tabpanel" aria-labelledby="nav-ctg-tab" tabindex="0">
        <div class="table-responsive">
            <table id="ctg-table" class="table table-striped w-100" data-url="<?= base_url('admin/load_transaction_page_table/'. encode(2)) ?>">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Reference #</th>
                        <th scope="col">OR #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact #</th>
                        <th scope="col">Email</th>
                        <th scope="col">Date Added</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

</div> -->