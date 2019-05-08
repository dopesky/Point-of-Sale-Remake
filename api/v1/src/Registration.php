<?php

class Registration{

	private $API_KEY=null;

	public function __construct($apikey = ''){
		$this->API_KEY = $apikey;
	}

	public function register($email,$usertype='admin',$return_headers = false){
		$data = http_build_query(array('email'=>$email));
		$curl = curl_init(SERVER_URL.'/auth/register/'.$usertype);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function confirm_token_validity($token,$id,$return_headers = false){
		$curl = curl_init(SERVER_URL.'/auth/check_token_validity/'.$token."/".$id);
		set_options($curl,$this->API_KEY);
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function reset_password($new_password,$repeat_password,$token,$id,$return_headers = false){
		$data = http_build_query(array('new_password'=>$new_password,'repeat_password'=>$repeat_password));
		$curl = curl_init(SERVER_URL.'/auth/password_reset/'.$token."/".$id);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function request_password_reset($email,$return_headers = false){
		$data = http_build_query(array('email'=>$email));
		$curl = curl_init(SERVER_URL.'/auth/request_reset');
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