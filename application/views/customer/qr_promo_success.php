<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="<?=$page_css_class?>">
<!-- <div class="" style="background-color: yellow;"> -->
    <div class="d-flex align-items-center justify-content-center mx-4" >
        <div class="d-flex flex-column justify-content-around <?=$page_css_class == 'ur-banner-bg' ? '' :'text-white'?>" style="height: 100vh">
            <div class="mt-5">
                <?=$form_brand_logo?>
                <!-- Main Message -->
                <h1 class="text-center font-chunkfive">Response Accepted!</h1>
				<hr style="color:white">
                <!-- <h1 class="text-center font-chunkfive"><?=$thanks_msg?>!</h1> -->
				<?php if (isset($form_id) && $form_id < 5): ?>
					<p class="text-center mt-2" style="font-size:1.5rem">Stay Tuned!</p>
					<div class="text-center m-auto" style="max-width:40rem">
						
						<!-- <p class="text-center mt-2" style="font-size:1.3rem">WE WILL BE GIVING AWAY <u><?=$prod_sale_count_name?></u> TODAY!</p>
						<p class="text-center mt-1" style="font-size:1.3rem">WINNERS WILL RECEIVE AN EMAIL WITHIN 24 HOURS</p> -->

						<p class="text-center mt-2" style="font-size:1.3rem">We're giving away <?=$brand_name?> products every day during the promo period!!</p>
						<p class="text-center mt-1" style="font-size:1.3rem">Every response counts—entries will be collected to give you more chances of winning!</p>
						<p class="text-center mt-1" style="font-size:1.3rem">Winners will be notified via email. Didn't win today? Don't worry—your entry stays in for the next draw!</p>


						<p class="text-center mt-1" style="font-size:1.3rem"><?=$hash_tag_msg?></p>
					</div>
				<?php else: ?>
					<div class="text-center m-auto" style="max-width:40rem">
						<p class="text-center mt-2" style="font-size:1.5rem"><?=isset($name) ? 'Hi '.$name.'!' : ''?></p>

						<p class="text-center mt-2" style="font-size:1.3rem">Thank you for joining our raffle! Your entry has been successfully received and recorded.</p>
						<p class="text-center mt-1" style="font-size:1.3rem">Winners will be drawn every week and will be announced via our social media pages and via email.</p>
						<p class="text-center mt-1" style="font-size:1.3rem">We truly appreciate your participation and support.</p>
						<p class="text-center mt-1" style="font-size:1.3rem"><?=$hash_tag_msg?></p>
					</div>
				<?php endif; ?>
            </div>
            <!-- Socials -->
            <?=$form_socials?>
        </div>
    </div>
    <!-- <div class="position-relative">
        <img class="endorser-img" src="<?= base_url('assets\img\VicSotto.png') ?>"> 
    </div> -->
</div>



