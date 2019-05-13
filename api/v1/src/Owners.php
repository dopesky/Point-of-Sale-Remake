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
}
?>