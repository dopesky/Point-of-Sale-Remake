<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	private $api_key;

	public function __construct(){
		parent::__construct();
		$this->load->library('email');
		$this->api_key = $this->apikeys_model->get_api_key($_SERVER['HTTP_APIKEY']);
		if(!$this->api_key){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid API Key Provided. Contact Admin!</span>'));
			exit;
		}
	}

	public function login(){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if(sizeof($_POST)<1){
			$this->common->set_headers(412);
			echo json_encode(array('status'=>412,'errors'=>'<br><br><span>Ensure All Required Fields are Filled!</span>'));
			return 412;
		}
		if($this->form_validator->run_rules('login') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$login_credentials = $this->users_model->get_user_by_email($this->input->post('email'));
		if($login_credentials){
			if(password_verify($this->input->post('password'), $login_credentials->password) && $login_credentials->suspended == 0){
				$this->users_model->update_user_details($login_credentials->user_id,array('token_expire'=>0,'last_access_time'=>$this->time->get_now()));
				$role = (!$login_credentials->owner_id && !$login_credentials->employee_id) || $login_credentials->company ? 'owner':'employee';
				$fname = ($login_credentials->first_name) ? $login_credentials->first_name : $login_credentials->owner_fname;
				$lname = ($login_credentials->last_name) ? $login_credentials->last_name : $login_credentials->owner_lname;
				$photo = ($login_credentials->profile_photo) ? $login_credentials->profile_photo : $login_credentials->owner_photo;
				$userdata = array('user_id'=>$login_credentials->user_id,'email'=>$login_credentials->email,'role'=>$role,'fname'=>$fname,'lname'=>$lname,'photo'=>$photo);
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>$userdata));
				return 202;
			}elseif(password_verify($this->input->post('password'), $login_credentials->password)){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Email not Verified!</span>'));
				return 409;
			}
		}
		$this->common->set_headers(400);
		echo json_encode(array('status'=>400,'errors'=>'<br><br><span>Invalid Email or Password!</span>'));
		return 400;
	}

	public function register($user){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('WRITE','BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if(sizeof($_POST)<1){
			$this->common->set_headers(412);
			echo json_encode(array('status'=>412,'errors'=>'<br><br><span>Ensure All Required Fields are Filled!</span>'));
			return 412;
		}
		switch($user){
			case 'admin':
				if($this->form_validator->run_rules('sign_up') == false){
					$this->common->set_headers(400);
					echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
					return 400;
				}
				$token=$this->common->get_crypto_safe_token(random_int(25, 30));
				$email = $this->input->post('email');
				$sign_up_response=$this->users_model->add_user(array('email'=>$email, 'token'=>$token));
				if (!$sign_up_response) {
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>Sign up failed. Please try again!</span>'));
					return 500;
				}
				$email_body = $this->email->get_email_body('intro', array('token_url'=>getenv('SITE_DOMAIN').'auth/reset_password/'.$token."/".$sign_up_response)); 
				$email_response = $this->email->send_email($email, 'Welcome to POS', $email_body, 'introduction');
				if ($email_response === true) {
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>"<br><br><span>Registration successful. Check your email for more information.</span>"));
					return 202;
				}else {
					$this->common->set_headers(503);
					echo json_encode(array('status'=>503,'errors'=>"<br><br><span>Email Verification Failed. Please Click Forgot Password on the Login Page To Receive Your Verification Email!</span>"));
					return 503;
				} 
				break;
			case 'owner':
				if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
					$this->common->set_headers(403);
					echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
					return 403;
				}
				if($this->form_validator->run_rules('sign_up_owner') == false){
					$this->common->set_headers(400);
					echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
					return 400;
				}
				$fname = $this->input->post('first_name');
				$lname = $this->input->post('last_name');
				$company = $this->input->post('company');
				$user_id = $this->input->post('user_id');
				$verification = $this->users_model->get_user_by_id($user_id);
				if(!$verification || $verification->suspended == 1){
					$this->common->set_headers(400);
					echo json_encode(array('status'=>400,'errors'=>'<br><br><span>Invalid User ID Provided!</span>'));
					return 400;
				}
				$data = array('first_name'=>$fname,'last_name'=>$lname,'company'=>$company,'user_id'=>$user_id);
				$database_response = $this->owners_model->add_owner($data);
				if($database_response){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Details Saved Successfully!</span>'));
					return 202;
				}else{
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>Details Not Saved. Contact Admin!</span>'));
					return 500;
				}
				break;
			default:
				$this->common->set_headers(400);
				echo json_encode(array('status'=>400,'errors'=>"<br><br><span>Unrecognized User Registration!</span>"));
					return 400;
				break;
		}
	}

	public function request_reset(){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if(sizeof($_POST)<1){
			$this->common->set_headers(412);
			echo json_encode(array('status'=>412,'errors'=>'<br><br><span>Ensure All Required Fields are Filled!</span>'));
			return 412;
		}
		if ($this->form_validator->run_rules('forgot_password')==false) {
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$email = $this->input->post('email');
		$user = $this->users_model->get_user_by_email($email); 
		if (!$user || ($user->suspended == 1 && $user->password)) {
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>'<br><br><span>Email not Registered on the System!</span>'));
			return 400;
		}
		$token = $this->common->get_crypto_safe_token(random_int(25, 30)); 
		$resave_token = $this->users_model->update_user_details($user->user_id,array('token'=>$token));
		if (!$resave_token) {
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again!</span>'));
			return 500;
		}
		$email_body = $this->email->get_email_body('password', array('token_url'=>getenv('SITE_DOMAIN').'auth/reset_password/'.$token."/".$user->user_id)); 
		$email_response = $this->email->send_email($user->email, 'Password Reset', $email_body, 'password-reset'); 
		if ($email_response===true) {
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>'<br><br><span>Password Reset Successful. Check your email for more information.</span>'));
			return 202;
		}else {
			$this->common->set_headers(503);
			echo json_encode(array('status'=>503,'errors'=>'<br><br><span>Password Reset Failed. Please Try Again!</span>'));
			return 503;
		}
	}

	public function check_token_validity($token,$id,$return_on_success = false){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH','READ'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$verification=$this->users_model->get_user_by_id($id);
		if (!$verification || ($verification->suspended == 1 && $verification->password)) {
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>'<br><br><span>User <b>NOT</b> Registered in the System. Please Register First!</span>'));
			return 400; 
		}
		if ($token!==$verification->token) {
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>'<br><br><span>Invalid token supplied.</span>'));
			return 400;
		}
		$time_to_expire = $this->time->add_time($verification->token_expire, $verification->last_access_time);
		if ($this->time->diff_date($this->time->get_now(), $time_to_expire)>0) {
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Reset Token has Expired. Please Request for Another Token.</span>'));
			return 409;
		}
		if(!$return_on_success){
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>''));
		}
		return 202;
	}

	public function password_reset($token,$id){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$verification = $this->check_token_validity($token,$id,true);
		if($verification !== 202) return $verification;
		if($this->form_validator->run_rules('password_reset') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$data = array('password'=>password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),'suspended'=>0,'token_expire'=>0);
		$database_response=$this->users_model->update_user_details($id,$data);
		$verification = $this->users_model->get_user_by_id($id);
		if ($database_response && $verification) {
			$_POST['email'] = $verification->email;
			$_POST['password'] = $this->input->post('new_password');
			return $this->login();
		}else {
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>Password Reset failed. Try again!</span>'));
			return 500;
		}
	}
}