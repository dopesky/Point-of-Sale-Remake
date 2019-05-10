<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {
	private $template = "templates/main/template";

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || $this->session->userdata('userdata')['role']!=='owner'){
			redirect(site_url('auth/log_out'),'location');
		}
		//csrfProtector::init();
		$this->load->library('jsons');
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
		$data['departments'] = $this->jsons->get_valid_departments_json(false);
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

	public function employ(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$department = $this->input->post('department');
		$email = $this->input->post('email');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$employ = new Owners(getenv('API_KEY'));
		$response = $employ->employ($user_id,$fname,$lname,$email,$department);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function update_employee_details(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$department = $this->input->post('department');
		$email = $this->input->post('email');
		$employee_id = $this->input->post('id');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$update = new Owners(getenv('API_KEY'));
		$response = $update->update_employee_details($user_id,$employee_id,$fname,$lname,$email,$department);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
		}
	}

	public function unemploy_reemploy_employee(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$employee_id = $this->input->post('id');
		$action = $this->input->post('action');
		$update = new Owners(getenv('API_KEY'));
		$response = $update->unemploy_reemploy_employee($action, $user_id, $employee_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function get_employees($user_id){
		return $this->jsons->get_employees_for_owner($user_id);
	}
}
