<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {

	private $api_key;

	public function __construct(){
		parent::__construct();
		$this->api_key = $this->apikeys_model->get_api_key($_SERVER['HTTP_APIKEY']);
		if(!$this->api_key){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid API Key Provided. Contact Admin!</span>'));
			exit;
		}
	}
}