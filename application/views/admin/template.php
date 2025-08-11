<!DOCTYPE html>
<html lang="en">
<style>

</style>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon" />

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/bootstrap.min.5-3-3.css')?>"/>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/datatables.min.css')?>">



	<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/jquery.mCustomScrollbar.min.css')?>"> -->
	 


	<link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>" />
	<link rel="stylesheet" href="<?=base_url('assets/css/select2-bootstrap-5-theme.min.css')?>" />
	

	<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/daterangepicker.css')?>" /> -->

	<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/lobibox.min.css')?>" /> -->

	<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/datepicker.min.css')?>" /> -->

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/sidebar-themes.css?v=1.0')?>">

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-font-face.min.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/free-v4-shims.min.css?v=0.1')?>">

    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/bs-stepper.min.css')?>"> -->

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/style.css?v=0.1')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/sidebar.css?v=1.0')?>">

	<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/custom-style.css')?>"> -->

	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/sweetalert2.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/flatpickr.min.css')?>">

	<title><?= $title . ' | ' . system_default()['system_name']?></title>
</head>

<body>
	<input type="hidden" value="<?=base_url($controller)?>" id="base_url">

	<?php $permission_path = $_SESSION[system_default()['session_name']]['user_permissions'] ?>
	
	<!-- <div class="overlay__outer">
		<div class="overlay__inner">
			<div class="overlay__content">
				<span class="spinner"></span>
			</div>
		</div>
	</div> -->

	<div class="page-wrapper legacy-theme toggled">
	<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
		<i class="fas fa-bars"></i>
	</a>
	
	<nav id="sidebar" class="sidebar-wrapper">
		<div class="sidebar-content">
			<div class="sidebar-brand">
				<a href="<?=base_url($controller)?>"><?=system_default()['system_name']?></a>
				<div id="close-sidebar">
				<i class="fas fa-times"></i>
				</div>
			</div>
			<div class="sidebar-header">
				<div class="user-pic">
					<!-- <img class="img-responsive img-rounded" src="<?=base_url('assets/img/user.jpg')?>"alt="User picture"> -->
					<div class="avatar-circle">
						<span class="initials">
							<?= $this->session->userdata(system_default()['session_name'])['full_name'][0] ?>
						</span>
					</div>
				</div>
				<div class="user-info">
					<span class="user-name">
						<strong><?= $this->session->userdata(system_default()['session_name'])['full_name'] ?></strong>
					</span>
					<span class="user-unit-location">
						<?= $this->session->userdata(system_default()['session_name'])['unit_location'] ?>
					</span>
					<span class="user-role">
						Administrator
					</span>
					<span class="user-status">
						<i class="fa fa-circle"></i>
						<span>Online</span>
					</span>
				</div>
			</div>
			<div class="sidebar-menu">
				<ul>
					<li class="header-menu">
						<span>General</span>
					</li>
					<li class="pe-none" >
						<a class="text-secondary-emphasis"href="<?=base_url($controller.'/dashboard')?>" tabindex="-1" aria-disabled="true">
							<i class="fas fa-home"></i>
							<span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="<?=base_url($controller.'/transaction-list')?>">
							<i class="fas fa-exchange-alt"></i>
							<span>Transaction List</span>
						</a>
					</li>
					
					<?php 
						// $general_links = ['cdi-survey-report', 'ctg-survey-report'];
						$general_links = $form_access_links;
						$uri = $this->uri->segment(2);
						$isActive = in_array(str_replace('_', '-', $uri), $general_links);
					?>

					<li class="sidebar-dropdown <?= $isActive ? 'active' : '' ?>">
						<a href="#">
							<i class="fas fa-table"></i>
							<span>Reports</span>
						</a>
						<div class="sidebar-submenu <?= $isActive ? 'd-block' : '' ?>">
							<ul>
								<?=@$report_menus?>
							</ul>
						</div>
					</li>
					
					<li class="header-menu">
						<span>Admin</span>
					</li>
					<?php 
						$admin_links = ['forms', 'employees', 'stores', 'outlets'];
						$uri_segment = str_replace('_', '-', $this->uri->segment(2));
						$isActive = in_array($uri_segment, $admin_links);
					?>

					<li class="sidebar-dropdown <?= $isActive ? 'active' : '' ?>">
						<a href="#">
							<i class="fas fa-cog"></i>
							<span>Maintenance</span>
						</a>
						<div class="sidebar-submenu <?= $isActive ? 'd-block' : '' ?>">
							<ul>
								<li>
									<a href="<?= base_url($controller . '/employees') ?>">Employees</a>
								</li>

								<?php if(in_array('read_form', $permission_path['form'])):?>
									<li>
										<a href="<?= base_url($controller . '/forms') ?>">Forms</a>
									</li>
								<?php endif;?>
								<!-- <li>
									<a href="<?= base_url($controller . '/stores') ?>">Stores</a>
								</li>
								<li>
									<a href="<?= base_url($controller . '/outlets') ?>">Outlets</a>
								</li> -->
							</ul>
						</div>
					</li>
					<hr class="border mx-2">
					<li>
						<a href="<?=base_url($controller.'/account-settings')?>">
							<i class="fas fa-user-cog"></i>
							<span>Account Settings</span>
						</a>
					</li>
				</ul>
			</div>
		<!-- sidebar-menu  -->
		</div>
		<!-- sidebar-content  -->
		<div class="sidebar-footer">
			<a href="<?=base_url('logout')?>">
				<i class="fa fa-power-off"></i>
			</a>
		</div>
	</nav>
	<!-- sidebar-wrapper  -->
	<main class="page-content">
		<div class="container-fluid">
			<div style="min-height: 85vh;">
				<?=$content?>	
			</div>
			<hr>
			<footer class="text-center stick-footer">
				<div class="mb-2">
					<small class="text-muted">
						<?=system_default()['system_name']?><?= $title ?><br>
						<?=system_default()['company_name']?>
					</small>
				</div>
			</footer>
		</div>
	</main>
	<!-- page-content" -->
	</div>
<!-- page-wrapper -->
</body>
	<script type="text/javascript" src="<?=base_url('assets/js/jquery-3.7.1.min.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/popper.min.js')?>"></script>
	
	<script type="text/javascript" src="<?=base_url('assets/js/bootstrap.min.5-3-3.js')?>"></script>


	<script type="text/javascript" src="<?=base_url('assets/js/font-awesome.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/datatables.min.js')?>"></script>




	<!-- <script type="text/javascript" src="<?=base_url('assets/js/jquery.mCustomScrollbar.concat.min.js')?>"></script> -->


	<!-- <script type="text/javascript" src="<?=base_url('assets/js/moment.min.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/daterangepicker.min.js')?>"></script> -->
	 
	
	<!-- <script type="text/javascript" src="<?=base_url('assets/js/dataTables.bootstrap4.min.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/select2-dropdownPosition.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/notifications.min.js')?>"></script> -->
    <!-- <script type="text/javascript" src="<?=base_url('assets/js/bs-stepper.min.js')?>" ></script> -->
	<!-- <script type="text/javascript" src="<?=base_url('assets/js/bootstrap-datepicker.min.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/custom-script.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/chart.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/qrcode.min.js')?>"></script> -->

	<!-- <script type="text/javascript" src="<?=base_url('assets/js/script.js')?>"></script>  -->
	<!-- <script type="text/javascript" src="<?=base_url('assets/js/admin/report/report_content_page.js')?>"></script> -->

	 
	<script type="text/javascript" src="<?=base_url('assets/js/sidebar.js?v=1.0')?>"></script> <!-- Get rid -->


	<script type="text/javascript" src="<?=base_url('assets/js/select2.full.min.js')?>"></script>

	<script type="text/javascript" src="<?=base_url('assets/js/sweetalert2.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/flatpickr.min.js')?>"></script> 

	<script type="text/javascript" src="<?=base_url('assets/js/admin/initElements.js')?>"></script>

	<!-- Dynamic Script Loader -->
	<?php if(isset($js_scripts)): ?>
		<?php foreach($js_scripts as $script): ?>
			<script data-type="D" type="text/javascript" src="<?=base_url('assets/js/admin/'.$script)?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>

<html>