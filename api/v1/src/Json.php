<?php

class Json{

	private $API_KEY = null;

	public function __construct($apikey = ''){
		$this->API_KEY = $apikey;
	}

	public function get_user_details($user_id, $return_headers = false){
		$curl = curl_init(SERVER_URL."/jsons/get_user_details/$user_id");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function get_valid_departments($return_headers = false){
		$curl = curl_init(SERVER_URL.'/jsons/get_valid_departments');
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function get_employees_by_owner_user_id($user_id, $check_suspended = true, $return_headers = false){
		$check_suspended = $check_suspended ? '1' : '0';
		$curl = curl_init(SERVER_URL."/jsons/get_employees_by_owner_user_id/$user_id/$check_suspended");
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}
}
?>