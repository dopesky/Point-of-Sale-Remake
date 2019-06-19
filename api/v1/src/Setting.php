<?php

class Setting{

	private $API_KEY=null;

	public function __construct($apikey = ''){
		$this->API_KEY = $apikey;
	}

	public function update_owner_details($user_id, $fname, $lname, $company, $photo_details, $return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'company'=>$company,'user_id'=>$user_id,'profile_photo'=>$photo_details));
		$curl = curl_init(SERVER_URL.'/settings/update_owner_details');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_employee_details($user_id, $fname, $lname, $photo_details, $return_headers = false){
		$data = http_build_query(array('first_name'=>$fname,'last_name'=>$lname,'user_id'=>$user_id,'profile_photo'=>$photo_details));
		$curl = curl_init(SERVER_URL.'/settings/update_employee_details');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function change_email($user_id, $email, $password, $return_headers = false){
		$data = http_build_query(array('user_id'=>$user_id,'email'=>$email,'password'=>$password));
		$curl = curl_init(SERVER_URL.'/settings/change_email');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function change_password($user_id, $password, $new_pass, $repeat_pass, $return_headers = false){
		$data = http_build_query(array('new_password'=>$new_pass,'password'=>$password,'repeat_password'=>$repeat_pass));
		$curl = curl_init(SERVER_URL.'/settings/change_password/'.$user_id);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_2FA($user_id, $action, $return_headers = false){
		$curl = curl_init(SERVER_URL."/settings/activate_deactivate_2FA/$user_id/$action");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_show_inactive($user_id, $action, $return_headers = false){
		$curl = curl_init(SERVER_URL."/settings/enable_disable_show_inactive/$user_id/$action");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_show_deleted($user_id, $action, $return_headers = false){
		$curl = curl_init(SERVER_URL."/settings/enable_disable_show_deleted/$user_id/$action");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function activate_deactivate_account($user_id, $action, $return_headers = false){
		$curl = curl_init(SERVER_URL."/settings/activate_deactivate_account/$user_id/$action");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function change_country($user_id, $country, $return_headers = false){
		$data = http_build_query(array('country'=>$country, 'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL."/settings/change_country");
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