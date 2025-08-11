<script type="text/javascript" src="<?= base_url('assets/js/shim.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/xlsx.full.min.js') ?>"></script>
<div class="container">
    <h3 class="text-center" style="display: block;">Upload Employee</h3><br />
    <div id="message-box"></div>
    <div class="row d-flex justify-content-center w-100">
        <div class="form-group">
            <div class="col-md-10">
                <form method="POST" action="" id="upload-form" enctype="multipart/form-data">
                    <input 
                        type="file" 
                        name="file-upload" 
                        class="form-control-file" 
                        id="file-upload"
                        >
                </form>
            </div>
        </div>
    </div>
    <div class="row d-flex justify-content-center w-100">
        <div class="form-group">
            <div class="col-md-12 text-left">
                <button type="button" data-url="/upload-employee-template" class="btn btn-primary btn-sm mb-2" id="upload-btn">Upload</button>
            </div>
        </div>
    </div>
    <div class="row table-responsive fixed-header" style="height: 700px; overflow-y: scroll;">
        <table class="table table-striped table-condensed table-export"
            data-sheet-name="Employee Upload Result" 
            data-file-name="Employee Upload Result">
            <thead class="thead-dark">
                <tr>
                    <th> User Type           </th>
                    <th> First Name          </th>
                    <th> Last Name           </th>
                    <th> Middle Initial      </th>
                    <th> Employee No         </th>
                    <th> Contact             </th>
                    <th> Email               </th>
                    <th> Unit                </th>
                    <th> location            </th>
                    <th> Location 2          </th>
                    <th> Remarks             </th>
                </tr> 
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>
</div>
<style>
    .fixed-header          { overflow-y: auto; height: 100px; }
    .fixed-header thead th { position: sticky; top: 0; }
    /* Just common table stuff. Really. */
    .fixed-header table  { border-collapse: collapse; width: 100%; }
    .fixed-header th, th { padding: 8px 16px; }
</style>