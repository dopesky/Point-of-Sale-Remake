<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jsons {

	public function get_user_details($user_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_user_details($user_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(false);
			return false;
		}
	}

	public function get_valid_departments_json($ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_valid_departments();
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(false);
			return false;
		}
	}

	public function get_employees_for_owner($user_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_employees_by_owner_user_id($user_id, false);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else if($response->status === 500){
			if($ajax_call) echo json_encode(array());
			return array();
		}else{
			if($ajax_call) echo json_encode(false);
			return false;
		}
	}
}
