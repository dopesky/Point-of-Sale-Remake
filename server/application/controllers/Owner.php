<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends MY_Controller {

	private $api_key;

	public function __construct(){
		parent::__construct();
		if(!isset($_SERVER['HTTP_APIKEY'])){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>No API Key Provided. Please provide API Key!</span>'));
			exit;
		}
		$this->load->library('email');
		$this->api_key = $this->apikeys_model->get_api_key($_SERVER['HTTP_APIKEY']);
		if(!$this->api_key){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid API Key Provided. Contact Admin!</span>'));
			exit;
		}
	}

	public function add_employee($owner_user_id){
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
		if($this->form_validator->run_rules('add_employee') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$fname = $this->input->post('first_name');
		$lname = $this->input->post('last_name');
		$email = $this->input->post('email');
		$department_id = $this->input->post('department_id');
		$owner_id = $this->owners_model->get_owner_by_user_id($owner_user_id)->owner_id;
		$token = $this->common->get_crypto_safe_token(random_int(25, 30));
		$data = array('first_name'=>$fname,'last_name'=>$lname,'email'=>$email,'department_id'=>$department_id,'owner_id'=>$owner_id,'token'=>$token);
		$add_successful = $this->employees_model->add_employee($data);
		if(!$add_successful){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$user_details = $this->users_model->get_user_by_email($email);
		$email_body = $this->email->get_email_body('intro', array('token_url'=>getenv('SITE_DOMAIN').'auth/reset_password/'.$token."/".$user_details->user_id)); 
		$email_response = $this->email->send_email($email, 'Welcome to POS', $email_body, 'introduction');
		if ($email_response === true) {
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>'<br><br><span>Successfully Added New Employee And a Verification Email has Been Sent to Them!</span>'));
			return 202;
		}else {
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>"<br><br><span>Email Verification Failed. Contact Admin!</span>"));
			return 500;
		}
	}

	public function update_employee_details($owner_user_id){
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
		if($this->form_validator->run_rules('update_employee') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}

		$owner_id = $this->users_model->get_user_by_id($owner_user_id);

		$employee_id = $this->input->post('employee_id');
		$fname = $this->input->post('first_name');
		$lname = $this->input->post('last_name');
		$email = $this->input->post('email');
		$department_id = $this->input->post('department_id');

		$token = $this->common->get_crypto_safe_token(random_int(25, 30));

		$employee_details = $this->employees_model->get_user_by_employee_id($employee_id);
		
		$department = $this->departments_model->get_department_by_id($department_id);

		if(!$employee_details || ($employee_details->suspended == 1 && $employee_details->password) || !$owner_id || !$department || !$owner_id->id_owner || $owner_id->suspended == 1 || $owner_id->owner_active == 0){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$owner_id = $owner_id->id_owner;
		$old_email = $employee_details->email;
		$data = array('first_name'=>$fname,'last_name'=>$lname,'email'=>$email,'department_id'=>$department_id,'token'=>$token,'old_email'=>$old_email);
		$response = $this->employees_model->update_employee_details_by_owner_and_employee_ids($employee_id,$employee_details->user_id,$owner_id,$data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		if($old_email !== $email){
			$email_body = $this->email->get_email_body('email_update', array('token_url'=>getenv('SITE_DOMAIN').'auth/reset_password/'.$token."/".$employee_details->user_id), ucwords($lname." ".$fname));
			$email_response = $this->email->send_email($email, 'Update User Email', $email_body, 'update-notification', ucwords($lname));
			if ($email_response === true) {
				$this->common->set_headers(202);
				echo json_encode(array('status'=>202,'response'=>'<br><br><span>Employee Details Successfully Updated. Employee has Received an Email to Notify Them.</span>'));
				return 202;
			}else {
				$this->common->set_headers(503);
				echo json_encode(array('status'=>503,'errors'=>"<br><br><span>Email Not Sent to Employee but New Details Saved!</span>"));
				return 503;
			}
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Employee Details Successfully Updated!</span>'));
		return 202;
	}

	public function unemploy_reemploy_employee($action){
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
		if($this->form_validator->run_rules('unemploy_reemploy_employee') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post('user_id');
		$employee_id = $this->input->post('employee_id');
		$owner_id = $this->users_model->get_user_by_id($user_id);
		$employee_id = $this->employees_model->get_user_by_employee_id($employee_id);
		if(!$owner_id || !$employee_id){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		if(($employee_id->suspended && $employee_id->password) || $owner_id->id_owner !== $employee_id->owner_id || $owner_id->suspended == 1 || $owner_id->owner_active == 0){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		switch ($action) {
			case 'unemploy':
				$response = $this->employees_model->unemploy_employee($employee_id->employee_id,$owner_id->id_owner);
				if($response){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Employee Unemployed Successfully!</span>'));
					return 202;
				}
				break;
			case 'reemploy':
				$response = $this->employees_model->reemploy_employee($employee_id->employee_id,$owner_id->id_owner);
				if($response){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Employee Reemployed Successfully!</span>'));
					return 202;
				}
				break;
			default:
				$this->common->set_headers(400);
				echo json_encode(array('status'=>400,'errors'=>'<br><br><span>No Valid Action Provided To Be Done on The Data!</span>'));
				return 400;
		}
		$this->common->set_headers(500);
		echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
		return 500;
	}

	public function add_product(){
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
		if($this->form_validator->run_rules('add_product') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$owner_user_id = $this->input->post('user_id');
		$owner_details = $this->users_model->get_user_by_id($owner_user_id);
		if(!$owner_details || !$owner_details->id_owner || ($owner_details->suspended == 1 && $owner_details->password) || $owner_details->owner_active == 0){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>"<br><br><span>Invalid Data Sent to Server!</span>"));
			return 400;
		}
		$name = $this->input->post('product');
		$category_id = $this->input->post('category');
		$cost = $this->input->post('cost');
		$owner_id = $owner_details->id_owner;
		$data = array('product'=>$name,'category_id'=>$category_id,'cost_per_unit'=>$cost,'owner_id'=>$owner_id);
		$add_successful = $this->products_model->add_product($data);
		if(!$add_successful){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Successfully Added New Product!</span>'));
		return 202;
	}

	public function update_product_details(){
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
		if($this->form_validator->run_rules('update_product') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}

		$owner_user_id = $this->input->post('user_id');

		$owner_id = $this->users_model->get_user_by_id($owner_user_id);

		$product_id = $this->input->post('product_id');
		$name = $this->input->post('product');
		$category_id = $this->input->post('category');
		$cost = $this->input->post('cost');

		$data = array('product'=>$name,'category_id'=>$category_id,'cost_per_unit'=>$cost);

		if(!$owner_id){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$response = $this->products_model->update_products_by_product_and_user_ids($product_id, $owner_user_id, $data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Product Details Successfully Updated!</span>'));
		return 202;
	}

	public function remove_readd_product($action){
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
		if($this->form_validator->run_rules('remove_readd_product') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post('user_id');
		$product_id = $this->input->post('product_id');
		$owner_id = $this->users_model->get_user_by_id($user_id);
		$product_details = $this->products_model->get_product_by_product_id($product_id, ($owner_id->show_inactive == 0), ($owner_id->show_deleted == 0));
		if(!$owner_id || !$product_details){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		if($product_details->suspended == 1 || $owner_id->suspended == 1 || $owner_id->id_owner !== $product_details->owner_id){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		switch ($action) {
			case 'deactivate':
				$response = $this->products_model->deactivate_product($product_details->product_id, $user_id);
				if($response){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Product Deactivated Successfully!</span>'));
					return 202;
				}
				break;
			case 'reactivate':
				$response = $this->products_model->reactivate_product($product_details->product_id, $user_id);
				if($response){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Product Reactivated Successfully!</span>'));
					return 202;
				}
				break;
			default:
				$this->common->set_headers(400);
				echo json_encode(array('status'=>400,'errors'=>'<br><br><span>No Valid Action Provided To Be Done on The Data!</span>'));
				return 400;
		}
		$this->common->set_headers(500);
		echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Try Again or Contact Admin!</span>'));
		return 500;
	}
}
