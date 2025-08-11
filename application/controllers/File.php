<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {

	public function __construct() {
    	parent::__construct();
    	$this->load->model('main_model', 'main');
        $this->controller = strtolower(__CLASS__);
    }

    public function attachment($file_name = '')
    {
        require_login($this->controller);
        if (!empty($file_name)) {
			if (preg_match('^[a-za-z0-9]{2,32}+[.]{1}[a-za-z]{3,4}$^', $file_name)) { // validation
				$file = upload_path() . 'attachments/' . $file_name;
				if (file_exists($file) == TRUE) {// check the file is existing 
					ob_get_level();
					@ob_end_clean();
					header('Content-Type: ' . mime_content_type($file));
					readfile($file);
				} else {
					show_404();
				}
			}
        }
        show_404();
    }

}

