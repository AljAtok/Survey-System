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

<div id="cdireports" data-value="<?=encode(1)?>">

    <div class="filter-container">
        <form class="container-fluid" method="post" action="<?= base_url( 'admin/survey_export/' . encode(1) )?>" target="_blank">
            <div class="row">
                <div class="col-12 col-sm-8">

                    <div class="row justify-content-center">
                        
                        <div class="mr-1" id="">
                            <label for="filter-list-start-date">Start Date:</label>
                            <input type="date" id="filter-list-start-date" name="start_date">
                        </div>

                        <div class="" id="">
                            <label for="filter-list-end-date">End Date:</label>
                            <input type="date" id="filter-list-end-date" name="end_date">
                        </div>

                        <div class="d-flex align-items-center">
                            <label class="m-0 mr-1" for="store-filter">Store:</label>
                            <select class="select2" id="store-filter" name="store_id" data-placeholder="All">
                            <option></option>
                                <?php foreach($stores as $store): ?>
                                    <option value="<?= $store->store_id ?>"><?= $store->store_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="col-12 col-sm-4 p-1">
                    <div class="">
                        <button class="btn btn-success w-100" type="submit"> Export <i class="fa fa-file-excel-o mr-1"></i> </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr>

    <div class="report-container">

        <div class="table-responsive">
            <table class="table table-striped mb-5 mt-3 w-100 report-data-table">
                <thead class="thead-dark">
                    <tr>
                        <th>Reference #</th>
                        <th>OR #</th>
                        <th>Store</th>
                        <th>Name</th>
                        <th>Contact #</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>StoreId</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($surveys as $row): ?>

                        <tr>
                            <td><?= decode($row->reference_number) ?></td>
                            <td><?= $row->or_number ?></td>
                            <td><?= $row->store_name ?></td>
                            <td><?= decode($row->name) ?></td>
                            <td><?= decode($row->contact_number) ?></td>
                            <td><?= decode($row->email) ?></td>
                            <td><?= $row->created_at ?></td>
                            <td><?= $row->store_id ?></td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <hr>

    <div class="chart-container text-center">
        <h4>Customer Demograpic</h4>
        <hr>
            
            <!-- <div class="border rounded p-1 w-50">
                <canvas id="age"></canvas>
            </div> -->

            <div class="d-flex justify-content-around">
                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Age</p>
                    <canvas id="age"></canvas>
                </div>

                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Gender</p>
                    <canvas id="gender"></canvas>
                </div>
            </div>

            <div class="d-flex justify-content-around mt-3">
                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Civil Status</p>
                    <canvas id="civilstatus"></canvas>
                </div>

                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Occupation</p>
                    <canvas id="occupation"></canvas>
                </div>
            </div>

    </div>

    
    <hr>

    <div class="chart-container text-center">
        <h4>Customer Feedback</h4>
        <hr>
        <div class="w-100 d-flex flex-column justify-content-between my-2">

            <div class="d-flex justify-content-around">
                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Dining Frequency</p>
                    <canvas id="diningfrequency"></canvas>
                </div>

                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Purchase Frequency</p>
                    <canvas id="purchasefrequency"></canvas>
                </div>
            </div>

            <!-- <div class="d-flex justify-content-around">
                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Dining Frequency</p>
                    <canvas style="width:100%" id="preferredfood"></canvas>
                </div>

                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Purchase Frequency</p>
                    <canvas style="width:100%" id="customersatisfaction"></canvas>
                </div>
            </div> -->

            <div class="m-auto p-1" style="width:80%">
                <!-- <p class="m-0">Customer Preference</p> -->
                <canvas id="preferredfood"></canvas>
            </div>

            <!-- <div class="d-flex justify-content-around mt-3">
                <div class="m-auto border rounded p-1" style="width:40%">
                    <p class="m-0">Service Satisfaction</p>
                    <canvas id="servicesatisfaction"></canvas>
                </div>

                <div class="m-auto border rounded p-1" style="width:40%">
                    <p class="m-0">Cleanliness Satisfaction</p>
                    <canvas id="cleanlinesssatisfaction"></canvas>
                </div>
            </div> -->

            <div class="d-flex justify-content-around mt-3">
                <!-- <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Favorite Flavor</p>
                    <canvas id="favoriteflavor"></canvas>
                </div> -->

                <div class="border rounded p-1" style="width:40%">
                    <p class="m-0">Will Recommend Chooks to Go?</p>
                    <canvas id="recommendationintent"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>