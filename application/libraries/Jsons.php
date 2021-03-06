<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jsons {

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

	public function get_valid_categories_json($ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_valid_categories();
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(false);
			return false;
		}
	}

	public function get_valid_payment_methods($ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_valid_payment_methods();
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

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

	public function get_employees_for_owner($user_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_employees_by_owner_user_id($user_id);
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

	public function get_products_for_owner($user_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_products_by_owner_user_id($user_id);
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

	public function get_products_for_purchases($owner_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_inventory_for_purchases($owner_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

	public function get_products_for_sale($owner_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_inventory_for_sales($owner_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

	public function get_purchases_by_owner_id($owner_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_purchases_by_owner_id($owner_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

	public function get_sales_by_owner_id($owner_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_sales_by_owner_id($owner_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

	public function get_all_products($owner_id, $ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_all_products($owner_id);
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}

	public function get_valid_countries($ajax_call = true){
		$REQUEST = new Json(getenv('API_KEY'));
		$response = $REQUEST->get_valid_countries();
		if($response->status === 202){
			if($ajax_call) echo json_encode($response->response);
			return $response->response;
		}else{
			if($ajax_call) echo json_encode(array());
			return array();
		}
	}
}
