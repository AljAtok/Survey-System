<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/bootstrap.min.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/login.css?v=1')?>">
    <title><?=system_default()['system_name']?> - <?=$title?></title>
</head>
<body class="login-wallpaper" id="<?=$title?>">
    <?php $uri = $this->uri->segment(2);?>
    <input type="hidden" value="<?=base_url()?>" id="base_url">
    <div id="container">
        <div class="d-flex align-items-center justify-content-center h-100">
            <div class="card login-card shadow">
                <div class="card-body">
                    <div class="row py-1">
                        <div class="col-md-12">
                            <div class="text-center">
                                <img src="<?=base_url('assets/img/svg/change-password.svg')?>" width="150px">
                            </div>
                            <br>
                            <?= $this->session->flashdata('message') ?>
                        </div>
                    </div>
                    <form method="post" action="<?= base_url('login/change_process')?>" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?=$employee_id?>">
                        <div class="form-group">
                            <label for="password">New Password *</label>
                            <input type="password" class="form-control" minlength="6" maxlength="16" id="password" name="password" placeholder="" value="" required>
                            <div class="invalid-feedback">
                                Password must be 6 characters minimum with a maximum of 16 characters.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="repeatPassword">Repeat New Password *</label>
                            <input type="password" class="form-control" minlength="6" maxlength="16" id="repeatPassword" name="repeat_password" placeholder="" value="" required>
                            <div class="invalid-feedback">
                                Repeat Password must be 6 characters minimum with a maximum of 16 characters and same as the password you provided above.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block my-4">Change Password</button>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <small class="text-muted">&#169; Bounty Agro Ventures, Inc.</small>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?=base_url('assets/js/jquery-3.3.1.min.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/js/popper.min.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/js/bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/js/font-awesome.js')?>"></script>
</body>
</html>