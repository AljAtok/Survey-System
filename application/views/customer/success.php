<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="banner-bg">
    <div class="d-flex align-items-center justify-content-center mx-4" >
        <div class="d-flex flex-column justify-content-around text-white" style="height: 100vh">
            <div class="mt-5">
                <div class="d-flex justify-content-center">
                    <img class="" src="<?= base_url('assets\img\chooks-logo-transparent.png') ?>"> 
                </div>
                <!-- Main Message -->
                <h1 class="text-center font-chunkfive">Thank you Ka-Chooks!</h1>
                <p class="text-center mt-2" style="font-size:1.1rem">Thank you for completing the survey. Your feedback is valuable to us.</p>
                <div class="text-center m-auto" style="max-width:40rem">
                    <p class="mb-5">Here's your reference code: <b><?= $reference_code ?></b> </p>
                    <!-- <div id="qrcode" class="d-inline-block p-2 bg-light border rounded" data-value="<?= $reference_code ?> "></div> -->
                    <p><small>Please present the receipt you used to answer the survey along with the code to avail the 20 pesos discount on your next visit in the store. The redemption is valid only for two weeks from your purchase date and can be used for a single transaction per code. Please be aware that this discount cannot to be combined with any other existing discounts and promos. Please note that Chooks to Go Inc. reserves the right to decline redemption if the code is deemed invalid or expired.</small></p>
                    <small><i>You can redeem the discount code at any of our stores, except for those inside the supermarkets.</i></small>
                </div>
            </div>
            <!-- Socials -->
            <div class="d-flex justify-content-around w-50 m-auto">
                <a href="https://www.facebook.com/chookstogo/" class="socials-icon" target="_blank"><h3 class="fab fa-facebook-f"></h3></a>
                <a href="https://www.instagram.com/chookstogoph/" class="socials-icon" target="_blank"><h3 class="fab fa-instagram"></h3></a>
                <a href="https://www.youtube.com/channel/UC1mn-pF58NABjDwMoaLeeyQ" class="socials-icon" target="_blank"><h3 class="fab fa-youtube"></h3></a>
            </div>
        </div>
    </div>
    <div class="position-relative">
        <img class="endorser-img" src="<?= base_url('assets\img\VicSotto.png') ?>"> 
    </div>
</div>



