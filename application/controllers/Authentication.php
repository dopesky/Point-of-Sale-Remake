<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {
	private $template = "templates/main/template";
	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		if(!array_key_exists('homepage_url',$_SESSION)){
			$data['content'] = "login_screen";
			$this->load->view($this->template,$data);
		}else{
			$data['content'] = "welcome_message";
			$this->load->view($this->template,$data);
			session_destroy();
		}
	}

	public function login(){
		if($this->form_validator->run_rules('login') == false){
			echo json_encode(array('ok'=>false,'errors'=>validation_errors('<br><br><span>','</span>')));
			return false;
		}
		$userdata = array('homepage_url'=>'Kevin');
		$this->session->set_userdata($userdata);
		echo json_encode(array('ok'=>true));
		return true;
	}
}
