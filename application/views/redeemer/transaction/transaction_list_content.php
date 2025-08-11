<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>

<div class="container-fluid" id="nav-tabContent">
    <?php if(decode($this->session->userdata(system_default()['session_name'])['employee_type_id']) == 2) : ?>
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
        
    <?php else: ?>
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
    <?php endif; ?>

</div>


