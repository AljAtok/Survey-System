<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/lobibox.min.css')?>" />
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/datepicker.min.css')?>" />
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/style.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-font-face.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-shims.min.css?v=0.1')?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/bootstrap.min.4-4-1.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/login.css?v=1')?>">
    <title><?=$title?> - Login</title>
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
                                <img src="<?=base_url('assets/img/svg/login-icon.svg')?>" width="150px">
                            </div>
                            <br>
                            <?= $this->session->flashdata('message') ?>
                        </div>
                    </div>
                    <form method="post" action="<?= base_url('login/login_process')?>" class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="">Email : *</label>
                            <input type="email" name="email" class="form-control" required>
                            <div class="invalid-feedback">
                                Email is required.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Password: *</label>
                            <input type="password" name="password" minlength="6" maxlength="16" class="form-control" required>
                            <div class="invalid-feedback">
                            Password is required.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block my-4">Login</button>
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
	<script type="text/javascript" src="<?=base_url('assets/js/bootstrap.min.4-4-1.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/font-awesome.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/notifications.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/bootstrap-datepicker.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/daterangepicker.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/jquery.dataTables.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/script.js?v=1.0')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/sidebar.js?v=1.0')?>"></script>

</body>
</html>