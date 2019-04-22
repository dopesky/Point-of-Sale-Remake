<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {
	private $template = "templates/main/template";

	public function __construct(){
		parent::__construct();
		$this->load->library('email');
	}

	public function index(){
		if(!array_key_exists('homepage_url',$_SESSION)){
			if($this->session->userdata('session_id')!==null) $this->log_out(base_url());
			$data['content'] = "login_screen";
			$this->load->view($this->template,$data);
		}else{
			$data['content'] = "welcome_message";
			$this->load->view($this->template,$data);
			session_destroy();
		}
	}

	public function login(){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		if($this->form_validator->run_rules('login') == false){
			echo json_encode(array('ok'=>false,'errors'=>validation_errors('<br><br><span>','</span>')));
			return false;
		}
		$login_credentials = $this->users_model->get_user_by_email($this->input->post('username'));
		if($login_credentials){
			if(password_verify($this->input->post('password'), $login_credentials->password) && $login_credentials->suspended == 0){
				$userdata = array('homepage_url'=>'Kevin');
				$this->session->set_userdata($userdata);
				echo json_encode(array('ok'=>true));
				return true;
			}elseif(password_verify($this->input->post('password'), $login_credentials->password)){
				echo json_encode(array('ok'=>false,'errors'=>'<br><br><span>Email not Verified!</span>'));
				return false;
			}
		}
		echo json_encode(array('ok'=>false,'errors'=>'<br><br><span>Invalid Email or Password!</span>'));
		return false;
	}

	public function forgot_password(){
		if($this->session->userdata('session_id')!==null) $this->log_out(site_url('auth/forgot_password'));
		$data['content'] = "forgot_password";
		$this->load->view($this->template,$data);
	}

	public function send_reset_email() {
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		if ($this->form_validator->run_rules('forgot_password')==false) {
			echo json_encode(array('ok'=>false,'errors'=>validation_errors('<br><br><span>','</span>')));
			return false;
		}
		$user=$this->users_model->get_user_by_email($this->input->post('username')); 
		if (!$user) {
			echo json_encode(array('ok'=>false,'errors'=>'<br><br><span>Email not Registered on the System!</span>'));
			return false; 
		}
		$token=$this->common->get_crypto_safe_token(random_int(25, 30)); 
		$resave_token=$this->users_model->update_user_details($user->user_id,array('token'=>$token));
		if (!$resave_token) {
			echo json_encode(array('ok'=>false,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return false; 
		}
		$email_body=$this->email->get_email_body('password', array('token_url'=>base_url('auth/reset_password/'.$token."/".$user->user_id))); 
		$email_response=$this->email->send_email($user->email, 'Password Reset', $email_body, 'password-reset'); 
		if ($email_response===true) {
			$this->session->set_flashdata('info', "<br><br><span>Password Reset Successful. Check your email for more information.</span>");
			echo json_encode(array('ok'=>true));
			return true;
		}else {
			echo json_encode(array('ok'=>false,'errors'=>"<br><br><span>Password Reset Failed. Contact Admin!</span>"));
			return false; 
		}
	}

	public function create_account(){
		if($this->session->userdata('session_id')!==null) $this->log_out(site_url('auth/create_account'));
		$data['content'] = "registration";
		$this->load->view($this->template,$data);
	}

	public function register($user){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		switch ($user) {
			case 'admin':
				if ($this->form_validator->run_rules('sign_up')==false) {
					echo json_encode(array('ok'=>false,'errors'=>validation_errors('<br><br><span>','</span>')));
					return false;
				}
				$token=$this->common->get_crypto_safe_token(random_int(25, 30)); 
				$sign_up_response=$this->users_model->add_user(array('email'=>$this->input->post('username'), 'token'=>$token)); 
				if (!$sign_up_response) {
					echo json_encode(array('ok'=>false,'errors'=>"<br><br><span>Sign up failed. Please try again!</span>"));
					return false;
				}
				$email_body=$this->email->get_email_body('intro', array('token_url'=>base_url('auth/reset_password/'.$token."/".$sign_up_response))); 
				$email_response=$this->email->send_email($this->input->post('username'), 'Welcome to POS', $email_body, 'introduction'); 
				if ($email_response===true) {
					$this->session->set_flashdata('info', "<br><br><span>Registration successful. Check your email for more information.</span>");
					echo json_encode(array('ok'=>true));
					return true;
				}else {
					echo json_encode(array('ok'=>false,'errors'=>"<br><br><span>Email Verification Failed. Contact Admin!</span>"));
					return false;
				} 
				break;
			case 'employee':
				# code...
				break;
			default:
				# code...
				break;
		}
	}

	public function reset_password($token,$id){
		if($this->session->userdata('session_id')!==null) $this->log_out(site_url("auth/reset_password/$token/$id"));
		$verification=$this->users_model->get_user_by_id($id);
		if (!$verification) {
			$this->session->set_flashdata('info', "<br><br><span>User <b>NOT</b> Registered in the System. Please Register First!</span>"); 
			redirect(site_url('auth/create_account'), 'location'); 
		}
		if ($token!==$verification->token) {
			$this->session->set_flashdata('info', "<br><br><span>Invalid token supplied.</span>"); 
			redirect(base_url(), 'location'); 
		}
		$time_to_expire = $this->time->add_time($verification->token_expire, $verification->last_access_time);
		if ($this->time->diff_date($this->time->get_now(), $time_to_expire)>0) {
			$this->session->set_flashdata('info', "<br><br><span>Reset Token has Expired. Please Request for Another Token.</span>"); 
			redirect(site_url('auth/forgot_password'), 'location'); 
		}
		$data['content'] = 'change_password';
		$data['id'] = $id;
		$this->load->view($this->template, $data); 
	}

	public function password_reset($id){
		if(sizeof($_POST)<1) redirect(base_url(),'location');
		$verification=$this->users_model->get_user_by_id($id);
		if($this->form_validator->run_rules('password_reset') == false){
			echo json_encode(array('ok'=>false,'errors'=>validation_errors('<br><br><span>','</span>')));
			return false;
		}
		$data = array('password'=>password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),'suspended'=>0,'token_expire'=>0);
		$database_response=$this->users_model->update_user_details($id,$data);
		if ($database_response) {
			$_POST['username'] = $verification->email;
			$_POST['password'] = $this->input->post('new_password');
			$login = $this->login(); 
			return $login;
		}else {
			echo json_encode(array('ok'=>false,'errors'=>'<br><br><span>Password Reset failed. Try again!</span>'));
			return false;
		}
	}

	public function log_out($url){
		session_destroy();
		redirect($url,'location');
	}

	function email_body(){
		echo $this->email->get_email_body('password',array('token_url'=>'https://example.com'));
	}
}
