<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common {
	public function get_crypto_safe_token($length){
	     $token = "";
	     $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	     $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	     $codeAlphabet.= "0123456789";
	     $max = strlen($codeAlphabet);

	    for ($i=0; $i < $length; $i++) {
	        $token .= $codeAlphabet[random_int(0, $max-1)];
	    }

	    return $token;
	}

	public function get_crypto_safe_code($length){
	     $token = "";
	     $codeAlphabet= "0123456789";
	     $max = strlen($codeAlphabet);

	    for ($i=0; $i < $length; $i++) {
	        $token .= $codeAlphabet[random_int(0, $max-1)];
	    }

	    return $token;
	}

	public function file_upload($target_dir,$file){
		$allowed_types = 'jpg|png|jpeg|pdf';

		$config['upload_path'] = $target_dir;
		$config['allowed_types'] = $allowed_types;
		$config['file_ext_tolower'] = true;
		$config['max_size'] = 2048;

		get_instance()->load->library('upload', $config);

        if (get_instance()->upload->do_upload($file))
        	return (object) array('ok'=>true,'file_name'=>get_instance()->upload->data('file_name'));
        else
        	return (object)array('ok'=>false,'errors'=>get_instance()->upload->display_errors('<br><br><span>','</span>'));
	}

	public function set_headers($code){
		header("Content-Type: application/json");
		header("HTTP/1.1 $code");
	}

	public function check_api_key_power($apikeypower,$requiredpowers = array()){
		if(!is_array($requiredpowers)) return false;
		return in_array(strtoupper($apikeypower), $requiredpowers);
	}
}