<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <link rel="icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/bootstrap.min.5-3-3.css')?>"/>

	<link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>" />
	<link rel="stylesheet" href="<?=base_url('assets/css/select2-bootstrap-5-theme.min.css')?>" />

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-font-face.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-shims.min.css?v=0.1')?>">

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/sweetalert2.min.css')?>">

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/customer/survey-page.css?v=0.3')?>"> 

	<title><?= $title?></title>
</head>

<body class="bg-dark" data-base-url="<?=base_url($controller)?>">
	
	<!-- sidebar-wrapper  -->
	<main class="page-content">
		<!-- <div class="container-fluid"> -->
			<div style="min-height: 85vh;">
				<?= $content ?>	
			</div>
			<footer class="text-center stick-footer p-2" style="background: black">
				<div class="pt-2">
					<p class="text-white">
						<?=isset($mod_form_name) ? $mod_form_name : system_default()['system_name']?><br>
						<strong>
							<?=isset($mod_footer_name) ? $mod_footer_name : system_default()['company_name']?>
						</strong>
					</p>
				</div>
				<div>
					<p>
						<small class="text-white">
							<!-- &copy; <?=date('Y')?> All Rights Reserved. Developed by <a href="https://www.facebook.com/chookstogo/" target="_blank" class="text-white"><?=isset($mod_footer_name) ? $mod_footer_name : system_default()['company_name']?></a> -->

							&copy; <?=date('Y')?> All Rights Reserved. Developed by <?=isset($mod_footer_name) ? $mod_footer_name : system_default()['company_name']?>

							<div>
								<!-- <script type="text/javascript"> //<![CDATA[
								var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
								document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
								//]]></script>
								<script language="JavaScript" type="text/javascript">
								TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_lg_222x54.png", "POSDV", "none");
								</script> -->

								<div class="d-flex justify-content-center">
									<img class="" width="120" src="<?=base_url('assets\img\positivessl_trust_seal_lg_222x54.png')?>">
								</div>
							</div>
						</small>
					</p>
				</div>
			</footer>
		<!-- </div> -->
	</main>
	<!-- page-content" -->
	</div>
<!-- page-wrapper -->

	
</body>
	<script type="text/javascript" src="<?=base_url('assets/js/jquery-3.7.1.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/bootstrap.min.5-3-3.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/popper.min.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/customer/initElements.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/select2.full.min.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/sweetalert2.min.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/font-awesome.js')?>"></script>




	<script type="text/javascript" src="<?=base_url('assets/js/customer/customer-validation.js?v=4.1')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/customer/customer-functionality.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/customer/customer-submission.js?v=1.0')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/customer/survey-script.js?v=1.0')?>"></script>
<html>
