<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item text-muted">File Maintenance</li>
		<li class="breadcrumb-item"><a class="text-muted" href="<?=base_url($controller.'/forms')?>">Forms</a></li>
		<li class="breadcrumb-item text-muted"><?=$title?></li>
		<!-- <li class="breadcrumb-item active" aria-current="page"></li> -->
	</ol>
</nav>

<!-- <form method="POST" action="<?= base_url($controller.'/validate-form/'.$form_id) ?>" class="needs-validation" enctype="multipart/form-data" id="form" novalidate>   -->
	<div class="container form-content-container">

		<div class="row" style="overflow: visible;">
			<div class="col-10">
				<!-- Form Begin  -->
					<div class="form-content" data-form-id="<?= $form_id ?>">
						<?= $form_content ?>
					</div>
				<!-- Form End -->
			</div>

			<div class="col border-left">
				
				<div class="p-2 position-fixed border-top border-bottom rounded bg-light">
					<div class="p-1">
						<button type="button" class="btn btn-sm btn-primary w-100 add-section"> 
							<span class="fas fa-plus-circle"></span> Add Section
						</button>
					</div>
				</div>
				

			</div>
		</div>	
	</div>
<!-- </form> -->

