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
}
?>