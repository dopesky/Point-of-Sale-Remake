<?php

class Owners{

	private $API_KEY=null;

	public function __construct($apikey = ''){
		$this->API_KEY = $apikey;
	}

	public function finish_registration($user_id,$fname,$lname,$company,$return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'company'=>$company,'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL.'/auth/register/owner');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function employ($user_id, $fname, $lname, $email, $department, $return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'department_id'=>$department,'email'=>$email));
		$curl = curl_init(SERVER_URL."/owner/add_employee/$user_id");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_employee_details($user_id, $employee_id, $fname, $lname, $email, $department_id, $return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'email'=>$email,'department_id'=>$department_id,'employee_id'=>$employee_id));
		$curl = curl_init(SERVER_URL."/owner/update_employee_details/$user_id");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function unemploy_reemploy_employee($action, $user_id, $employee_id, $return_headers = false){
		$data = http_build_query(array('employee_id'=>$employee_id,'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL."/owner/unemploy_reemploy_employee/$action");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_owner_details($user_id, $fname, $lname, $company, $photo_details, $return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'company'=>$company,'user_id'=>$user_id,'profile_photo'=>$photo_details));
		$curl = curl_init(SERVER_URL.'/owner/update_owner_details');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function change_owner_email($user_id, $email, $password, $return_headers = false){
		$data = http_build_query(array('user_id'=>$user_id,'email'=>$email,'password'=>$password));
		$curl = curl_init(SERVER_URL.'/owner/change_owner_email');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function change_owner_password($user_id, $password, $new_pass, $repeat_pass, $return_headers = false){
		$data = http_build_query(array('new_password'=>$new_pass,'password'=>$password,'repeat_password'=>$repeat_pass));
		$curl = curl_init(SERVER_URL.'/owner/change_owner_password/'.$user_id);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_2FA($user_id, $action, $return_headers = false){
		$curl = curl_init(SERVER_URL."/owner/activate_deactivate_2FA/$user_id/$action");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function register_product($user_id, $product, $category, $cost, $return_headers = false){
		$data = http_build_query(array('product'=>$product,'category'=>$category,'cost'=>$cost));
		$curl = curl_init(SERVER_URL."/owner/add_product/$user_id");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_product_details($user_id, $product, $category, $cost, $product_id, $return_headers = false){
		$data = http_build_query(array('product'=>$product,'category'=>$category,'cost'=>$cost,'product_id'=>$product_id));
		$curl = curl_init(SERVER_URL."/owner/update_product_details/$user_id");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_product($action, $user_id, $product_id, $return_headers = false){
		$data = http_build_query(array('product_id'=>$product_id,'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL."/owner/remove_readd_product/$action");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}
}
?>