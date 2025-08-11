<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('encode')){

    //edit starts here
	function encode($token){
		ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
		
        $key            = "h@���_�Ba:��!Ҷ��|5F~��w��@�ǃ�9�G��P�5w|������� *?̒�5AM9j	���o���X�r���6���ƈ.��������Oi/�z8�-�3�-Q#D��w ��W8��~�d;C�|�����'�.�vٻ=ǢM-����T��d-����G��!r>5EԄ����vr~]uY�x4�$";
        $ivlen          = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv             = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($token, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac           = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext     = base64url_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
	}

	function decode($token){
		ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $key   = "h@���_�Ba:��!Ҷ��|5F~��w��@�ǃ�9�G��P�5w|������� *?̒�5AM9j	���o���X�r���6���ƈ.��������Oi/�z8�-�3�-Q#D��w ��W8��~�d;C�|�����'�.�vٻ=ǢM-����T��d-����G��!r>5EԄ����vr~]uY�x4�$";
        $c     = base64url_decode($token);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv    = substr($c, 0, $ivlen);

        $hmac               = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw     = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac            = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
            return $original_plaintext;
        } 
    }

    function base64url_encode($data)
    { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 
    
    function base64url_decode($data)
    { 
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    } 
    //edits ends here

	function clean_data($data){
		$instanceName =& get_instance();
		$instanceName->load->helper('security');
		$clean = $instanceName->security->xss_clean($instanceName->db->escape_str($data));
		return trim($clean);
	}

	function generate_random($length){
		$random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		return $random;
	}

	function generate_random_coupon($length){
		/*$random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);*/
		//Remove 0, 1, i, l, o, I, L, O,
		$random = substr(str_shuffle("23456789ABCDEFGHJKMNPQRSTUVWXYZ"), 0, $length);
		return $random;
	}

	function create_id($format, $count){
		
		if($count > 0 && $count < 10){
			$id = $format . '00000' . $count;
		}elseif($count >= 10 && $count <= 99){
			$id = $format . '0000' . $count;
		}elseif($count >= 100 && $count <= 999){
			$id = $format . '000' . $count;
		}elseif($count >= 1000 && $count <= 9999){
			$id = $format . '00' . $count;
		}elseif($count >= 10000 && $count <= 99999){
			$id = $format . '0' . $count;
		}else{
			$id = $format . $count;
		}

		return $id;
	}

	function date_now(){
		$date = date('Y-m-d H:i:s');
		return $date;
	}

	function check_num($num){
		if(!is_null($num)){
			return $num;
		}else{
			return 0;
		}
	}

	function check_null($num){
		if(!is_null($num) || $num == 0){
			return $num;
		}else{
			return null;
		}
	}

	function check_array($var){
			return $var;
		if(isset($var)){
		}else{
			return 0;
		}
	}

	function convert_num($value){
		if($value >= 1000000000){
			$value = $value/1000000000;
			$value = number_format($value, 2) . ' B';
		}else if($value >= 1000000 && $value < 1000000000){
			$value = $value/1000000;
			$value = number_format($value, 2) . ' M';
		}else if($value > 1000 && $value < 1000000){
			$value = $value/1000;
			$value = number_format($value) . ' K';
		}else if($value > 99 && $value < 999){
			$value = $value/1000;
			$value = number_format($value, 2) . ' K';
		}else{
			 $value = '';
		}
		return $value;
    }
    

    function itexmo($number,$message,$apicode){
		$url = 'https://www.itexmo.com/php_api/api.php';
		$itexmo = array('1' => $number, '2' => $message, '3' => $apicode, 'passwd' => 'vm!3175)w!', '6' => 'CHOOKS');
		$param = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($itexmo),
		    ),
		);
		$context  = stream_context_create($param);
		return file_get_contents($url, false, $context);
	}


    function compressImage($source, $destination, $quality) { 
        // Get image info 
        $imgInfo = getimagesize($source);
        $mime    = $imgInfo['mime'];
         
        // Create a new image from file 
        switch($mime){ 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($source); 
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($source); 
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($source); 
                break; 
            default: 
                $image = imagecreatefromjpeg($source); 
        } 
         
        imagejpeg($image, $destination, $quality);
    } 


}
