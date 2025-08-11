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
                <?=$headline?>
				<hr style="color:white">
                <!-- <h1 class="text-center font-chunkfive"><?=$thanks_msg?>!</h1> -->
                <div class="text-center m-auto" style="max-width:40rem">
                    
					<?=$message?>
					<p class="text-center mt-1" style="font-size:1.3rem"><?=$hash_tag_msg?></p>
                </div>
            </div>
            <!-- Socials -->
            <?=$form_socials?>
        </div>
    </div>
    <!-- <div class="position-relative">
        <img class="endorser-img" src="<?= base_url('assets\img\VicSotto.png') ?>"> 
    </div> -->
</div>



