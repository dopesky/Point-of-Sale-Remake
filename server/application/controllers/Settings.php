<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

	private $api_key;

	public function __construct(){
		parent::__construct();
		if(!isset($_SERVER['HTTP_APIKEY'])){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>No API Key Provided. Please provide API Key!</span>'));
			exit;
		}
		$this->api_key = $this->apikeys_model->get_api_key($_SERVER['HTTP_APIKEY']);
		if(!$this->api_key){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid API Key Provided. Contact Admin!</span>'));
			exit;
		}
		$this->load->library('media');
	}

	public function update_owner_details(){
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
		if($this->form_validator->run_rules('update_owner') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post('user_id');
		$fname = $this->input->post('first_name');
		$lname = $this->input->post('last_name');
		$company = $this->input->post('company');
		$photo = $this->input->post('profile_photo');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !$user_details->id_owner || ($user_details->suspended == 1 && $user_details->password)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		$data = array('first_name'=>$fname,'last_name'=>$lname,'company'=>$company);
		if($photo && is_array($photo)){
			$upload_response = $this->media->upload_file($photo, $user_id);
			if(!$upload_response['ok']){
				$this->common->set_headers(500);
				echo json_encode(array('status'=>500,'errors' => $upload_response['msg']));
				return 500;
			}
			$data['profile_photo'] = $upload_response['file_name'];
		}
		$file_name = isset($data['profile_photo']) ? $data['profile_photo'] : null;
		$response = $this->owners_model->update_owner_by_user_id($user_id, $data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202, 'photo' => $file_name, 'response'=>'<br><br><span>Details Updated Successfully!</span>'));
		return 202;
	}

	public function update_employee_details(){
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
		if($this->form_validator->run_rules('update_employee_self') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post('user_id');
		$fname = $this->input->post('first_name');
		$lname = $this->input->post('last_name');
		$photo = $this->input->post('profile_photo');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !$user_details->owner_id || ($user_details->suspended == 1 && $user_details->password)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
		if(!$owner_details){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		$data = array('first_name' => $fname,'last_name' => $lname);
		if($photo && is_array($photo)){
			$upload_response = $this->media->upload_file($photo, $user_id);
			if(!$upload_response['ok']){
				$this->common->set_headers(500);
				echo json_encode(array('status'=>500,'errors' => $upload_response['msg']));
				return 500;
			}
			$data['profile_photo'] = $upload_response['file_name'];
		}
		$file_name = isset($data['profile_photo']) ? $data['profile_photo'] : null;
		$response = $this->employees_model->update_employee_by_user_id($user_id, $data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202, 'photo' => $file_name, 'response'=>'<br><br><span>Details Updated Successfully!</span>'.$fname));
		return 202;
	}

	public function change_email(){
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
		if($this->form_validator->run_rules('change_email') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$user_id = $this->input->post('user_id');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		if(!password_verify($password, $user_details->password)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Wrong/Invalid Password!</span>'));
			return 409;
		}
		if(strtolower($user_details->email) === $email){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Please Provide a Different Email From the One Used to Register!</span>'));
			return 409;	
		}
		$update = $this->users_model->update_user_details($user_id, array('email' => $email, 'token_expire' => 0));
		if(!$update){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Email Updated Successfully!</span>'));
		return 202;
	}

	public function change_password($user_id){
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
		if($this->form_validator->run_rules('change_password') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$password = $this->input->post('password');
		$new_password = $this->input->post('new_password');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		if(!password_verify($password, $user_details->password)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Wrong/Invalid Password!</span>'));
			return 409;	
		}
		$update = $this->users_model->update_user_details($user_id, array('password'=>password_hash($new_password, PASSWORD_DEFAULT),'token_expire'=>0));
		if(!$update){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Password Updated Successfully!</span>'));
		return 202;
	}

	public function activate_deactivate_2FA($user_id, $action){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		switch ($action) {
			case 'activate':
				$authenticator = new PHPGangsta_GoogleAuthenticator();
				$token = ($user_details->twofactor_secret) ? $user_details->twofactor_secret : $authenticator->createSecret();
				$data = ($token !== $user_details->twofactor_secret) ? array('twofactor_secret'=>$token,'twofactor_auth'=>1,'token_expire'=>0) : array('twofactor_auth'=>1,'token_expire'=>0);
				$response = $this->users_model->update_user_details($user_id, $data);
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$qrCodeUrl = $authenticator->getQRCodeGoogleUrl('Point of Sale ('.$user_details->email.")", $token);
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>array('url'=>$qrCodeUrl, 'secret'=>$token)));
				return 202;
			case 'deactivate':
				$response = $this->users_model->update_user_details($user_id, array('twofactor_auth'=>0,'token_expire'=>0));
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>"<br><br><span>Two Factor Authentication Deactivated!</span>"));
				return 202;
			default:
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
		}
	}

	public function enable_disable_show_inactive($user_id, $action){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		switch ($action) {
			case 'activate':
				$data = array('show_inactive' => 1);
				$response = $this->users_model->update_user_details($user_id, $data);
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>'<br><br><span>Functionality Activated!</span>'));
				return 202;
			case 'deactivate':
				$response = $this->users_model->update_user_details($user_id, array('show_inactive' => 0));
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>"<br><br><span>Functionality Deactivated!</span>"));
				return 202;
			default:
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
		}
	}

	public function enable_disable_show_deleted($user_id, $action){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		switch ($action) {
			case 'activate':
				$data = array('show_deleted' => 1);
				$response = $this->users_model->update_user_details($user_id, $data);
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>'<br><br><span>Functionality Activated!</span>'));
				return 202;
			case 'deactivate':
				$response = $this->users_model->update_user_details($user_id, array('show_deleted' => 0));
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>"<br><br><span>Functionality Deactivated!</span>"));
				return 202;
			default:
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
		}
	}

	public function activate_deactivate_account($user_id, $action){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || ($user_details->suspended == 1 && $user_details->password)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		switch ($action) {
			case 'activate':
				if($user_details->owner_id){
					$data = array('active' => 0);
				}else{
					$data = array('owner_active' => 0);
				}
				$response = $this->users_model->update_user_details($user_id, $data);
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>'<br><br><span>Account Activated!</span>'));
				return 202;
			case 'deactivate':
				if($user_details->owner_id){
					$data = array('active' => 1);
				}else{
					$data = array('owner_active' => 1);
				}
				$response = $this->users_model->update_user_details($user_id, $data);
				if(!$response){
					$this->common->set_headers(500);
					echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
					return 500;
				}
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>"<br><br><span>Account Deactivated!</span>"));
				return 202;
			default:
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
		}
	}

	public function change_country(){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if($this->form_validator->run_rules('change_country') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post('user_id');
		$country = $this->input->post('country');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;	
		}
		$country_details = $this->countries_model->get_country_by_name($country, true);
		if(!$country_details){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>That Country does not Exist in our System!</span>'));
			return 409;	
		}
		$data = array('country_id' => $country_details->country_id);
		$response = $this->users_model->update_user_details($user_id, $data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Country Changed!</span>'));
		return 202;
	}
}