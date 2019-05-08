<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {
	private $template = "templates/main/template";

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || $this->session->userdata('userdata')['role']!=='owner'){
			redirect(site_url('auth/log_out'),'location');
		}
		csrfProtector::init();
	}

	public function index(){
		if($this->session->userdata('userdata')['fname']){
			$data['content'] = 'owner_dashboard';
			$data['navbar'] = 'navbars/owner_navbar';
			$this->load->view($this->template,$data);
		}else{
			$data['content'] = 'owner_registration';
			$this->load->view($this->template,$data);
		}
	}

	public function manage_employees(){
		$data['content'] = 'manage_employees';
		$data['navbar'] = 'navbars/owner_navbar';
		$this->load->view($this->template,$data);
	}

	public function register_owner(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$company = $this->input->post('company');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$register = new Owners(getenv('API_KEY'));
		$response = $register->finish_registration($user_id,$fname,$lname,$company);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['fname'] = trim(strtolower($fname));
			$userdata['lname'] = trim(strtolower($lname));
			$this->session->set_userdata('userdata',$userdata);
			$this->session->set_flashdata('info',$response->response);
			echo json_encode(array('ok'=>true));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}
}
