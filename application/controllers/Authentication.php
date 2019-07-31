<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {
	private $template = "templates/main/template";

	public function __construct(){
		parent::__construct();
		//csrfProtector::init();
	}

	public function index(){
		if(!isset($_SESSION['userdata']['homepage_url']) && !isset($_SESSION['tempdata']['homepage_url'])){
			if($this->session->userdata('userdata')!==null) $this->log_out(base_url());
			$data['content'] = "login_screen";
			$this->load->view($this->template,$data);
		}else if(isset($_SESSION['userdata']['homepage_url'])){
			redirect($this->session->userdata('userdata')['homepage_url'],'location');
		}else{
			redirect($this->session->userdata('tempdata')['homepage_url'],'location');	
		}
	}

	public function forgot_password(){
		if($this->session->userdata('userdata')!==null) $this->log_out(site_url('auth/forgot_password'));
		$data['content'] = "forgot_password";
		$this->load->view($this->template,$data);
	}

	public function create_account(){
		if($this->session->userdata('userdata')!==null) $this->log_out(site_url('auth/create_account'));
		$data['content'] = "registration";
		$this->load->view($this->template,$data);
	}

	public function reset_password($token,$id){
		if($this->session->userdata('userdata')!==null) $this->log_out(site_url("auth/reset_password/$token/$id"));
		$check_validity = new Registration(getenv('API_KEY'));
		$response = $check_validity->confirm_token_validity($token,$id);
		if($response->status === 202){
			$data['content'] = 'change_password';
			$data['id'] = $id;
			$data['token'] = $token;
			$this->load->view($this->template, $data); 
			return;
		}
		$this->session->set_flashdata('info',$response->errors);
		if($response->status === 409){
			redirect(site_url('auth/forgot_password'),'location');
		}else{
			redirect(base_url(),'location');
		}
	}

	public function two_factor_auth($user_id){
		if($this->session->userdata('tempdata') === null) redirect(base_url(),'location');
		$data['content'] = 'two_factor_auth';
		$data['user_id'] = $user_id;
		$this->load->view($this->template,$data);
	}

	public function send_email_otp(){
		if(!isset($_SESSION['tempdata'])) redirect(base_url(),'location');
		$otp = new Registration(getenv('API_KEY'));
		$response = $otp->send_2_step_otp($this->session->userdata('tempdata')['user_id']);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
			return true;
		}else{
			echo json_encode(array('ok'=>false, 'code'=>$response->status, 'errors'=>$response->errors));
			return false;
		}
	}

	public function verify_otp_token(){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$check_validity = new Registration(getenv('API_KEY'));
		$response = $check_validity->verify_2_step_code($_POST['code'],$this->session->userdata('tempdata')['user_id']);
		if($response->status == 202){
			$userdata = $this->session->userdata('tempdata');
			$this->session->unset_userdata('tempdata');
			$userdata['homepage_url'] = $userdata['role'] == 'owner' ? site_url('owner'):site_url('employee');
			$this->session->set_userdata('userdata',$userdata);
			echo json_encode(array('ok'=>true));
			return true;
		}else{
			echo json_encode(array('ok'=>false, 'errors'=>$response->errors));
			return false;
		}
	}

	public function verify_google_auth_token(){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$check_validity = new Registration(getenv('API_KEY'));
		$response = $check_validity->verify_google_auth_code($_POST['code'],$this->session->userdata('tempdata')['user_id']);
		if($response->status == 202){
			$userdata = $this->session->userdata('tempdata');
			$this->session->unset_userdata('tempdata');
			$userdata['homepage_url'] = $userdata['role'] == 'owner' ? site_url('owner'):site_url('employee');
			$this->session->set_userdata('userdata',$userdata);
			echo json_encode(array('ok'=>true,'response'=>$response->response));
			return true;
		}else{
			echo json_encode(array('ok'=>false, 'code'=> $response->status, 'errors'=>$response->errors));
			return false;
		}
	}

	public function login(){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$login = new Login(getenv('API_KEY'));
		$email = $this->input->post('username');
		$password = $this->input->post('password');
		$response = $login->login($email,$password);
		if($response->status == 202 || $response->status === 200){
			$this->set_userdata($response);
			echo json_encode(array('ok'=>true));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function register(){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$registration = new Registration(getenv('API_KEY'));
		$email = $this->input->post('username');
		$response = $registration->register($email);
		if($response->status == 202){
			$this->session->set_flashdata('info',$response->response);
			echo json_encode(array('ok'=>true));
			return true;
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
			return false;
		}
	}

	public function send_reset_email() {
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$registration = new Registration(getenv('API_KEY'));
		$email = $this->input->post('username');
		$response = $registration->request_password_reset($email);
		if($response->status == 202){
			$this->session->set_flashdata('info',$response->response);
			echo json_encode(array('ok'=>true));
			return true;
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
			return false;
		}
	}

	public function password_reset($token,$id){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$new_password = $this->input->post('new_password');
		$repeat_password = $this->input->post('repeat_password');
		$password_reset = new Registration(getenv('API_KEY'));
		$response = $password_reset->reset_password($new_password,$repeat_password,$token,$id);
		if($response->status === 202 || $response->status === 200){
			$this->set_userdata($response);
			echo json_encode(array('ok'=>true));
			return true;
		}
		echo (json_encode(array('ok'=>false,'errors'=>$response->errors)));
		return false;
	}

	public function log_out($url = null){
		if(!$url) $url = base_url();
		$this->session->unset_userdata('userdata');
		redirect($url,'location');
	}

	private function set_userdata($response){
		$response->response = json_decode(json_encode($response->response),true);
		if($response->status === 200){
			$response->response['homepage_url'] = site_url('auth/two_factor_auth/'.$response->response['user_id']);
			$this->session->set_userdata('tempdata',$response->response);
		}else{
			$response->response['homepage_url'] = $response->response['role'] == 'owner' ? site_url('owner'):site_url('employee');
			$this->session->set_userdata('userdata',$response->response);
		}
	}
}
