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

	public function get_random_color_array(){
		return array('#2196f3','#009688','#f44336','#4caf50','#e91e63','#9c27b0','#3f51b5','#ff9800','#795548','#ffc107');
	}

	public function get_random_color(){
		$colors = $this->get_random_color_array();
		return $colors[random_int(0, 9)];
	}
}