<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends CI_Controller {

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
	}

	public function add_purchase(){
		$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>$_POST));
			return 500;
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if(sizeof($_POST) < 1 || sizeof($_POST['data']) < 1 || !is_array($_POST['data'])){
			$this->common->set_headers(412);
			echo json_encode(array('status'=>412,'errors'=>'<br><br><span>Ensure All Required Fields are Filled!</span>'));
			return 412;
		}
		$pay_for = $this->payments_model->get_payfor_by_name('purchase');
		if(!$pay_for){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$data = array();
		$amount = 0;
		foreach ($_POST['data'] as $key => $value) {
			$product_id = $value['product_id'];
			$user_id = $this->input->post('user_id');
			$quantity = $value['quantity'];
			$cost = $value['total_cost'];
			$discount = $value['discount'];
			if(isset($method_id) && $method_id !== $value['method_id']){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			$method_id = $value['method_id'];
			$post_data = array('product_id' => $product_id, 'user_id' => $user_id, 'quantity' => $quantity, 'total_cost' => $cost, 'discount' => $discount, 'method_id' => $method_id);
			if($this->form_validator->run_add_purchase_rules($post_data) == false){
				$this->common->set_headers(400);
				echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
				return 400;
			}
			$user_details = $this->users_model->get_user_by_id($user_id);
			if(!$user_details || !strpos(strtolower($user_details->status), "active")){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			if($user_details->owner_id){
				$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
				if(!$owner_details){
					$this->common->set_headers(409);
					echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
					return 409;
				}
			}
			$product_details = $this->products_model->get_product_by_product_id($product_id, true, true);
			if(!$product_details || !strpos(strtolower($product_details->status), "active") || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			$method = $this->payments_model->get_payment_method_by_id($method_id);
			if(!$method){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Invalid Payment Method!</span>'));
				return 409;
			}
			$payment_id = $this->payments_model->add_payment(array('payfor_id' => $pay_for->payfor_id, 'method_id' => $method_id));
			if(!$payment_id){
				$this->common->set_headers(500);
				echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Unable to Add Payment. Contact Admin!</span>'));
				return 500;
			}
			$data[] = array('product_id' => $product_id, 'recorded_by' => $user_id, 'modified_by' => $user_id, 'quantity' => $quantity, 'total_cost' => $cost, 'discount' => $discount, 'payment_id' => $payment_id);
			$amount += (int) $cost;
		}
		if($amount < 0 || sizeof($data) < 1){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$response = $this->purchases_model->add_purchases_batch($data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Purchase(s) Recorded Successfully!</span>'));
		return 202;
	}

	public function modify_purchase(){
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
		if($this->form_validator->run_rules('edit_purchase') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$purchase_id = $this->input->post('purchase_id');
		$product_id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');
		$quantity = $this->input->post('quantity');
		$cost = $this->input->post('total_cost');
		$discount = $this->input->post('discount');
		$method_id = $this->input->post('method_id');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		if($user_details->owner_id){
			$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
			if(!$owner_details){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
		}
		$original_purchase = $this->purchases_model->get_purchase_by_id($purchase_id, ($user_details->show_inactive == 0), true);
		if(!$original_purchase){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Are NOT Allowed to Update That Purchase!</span>'));
			return 409;
		}
		$product_details = $this->products_model->get_product_by_product_id($product_id, false, true);
		if(!$product_details || $product_details->suspended == 1 || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$original_product = $this->products_model->get_product_with_inventory_details($original_purchase->product_id, false, true);

		if($original_purchase->product_id != $product_id){
			$new_level = (int)$original_product->inventory_level - (int)$original_purchase->quantity;
			if($new_level < 0){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
				return 409;
			}
			if($product_details->active != 1){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Add That Product Since It has Been Disabled!</span>'));
				return 409;
			}
		}else{
			$new_level = (int)$original_product->inventory_level - (int)$original_purchase->quantity + (int)$quantity;
			if($new_level < 0){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
				return 409;
			}
		}

		$method = $this->payments_model->get_payment_method_by_id($method_id);
		if(!$method){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}

		$data = array('product_id'=>$product_id,'modified_by'=>$user_id,'quantity'=>$quantity,'total_cost'=>$cost,'discount'=>$discount);
		$payment_data = array('payment_id' => $original_purchase->payment_id, 'method_id' => $method_id);
		$response = $this->purchases_model->update_purchase($purchase_id, $data, $payment_data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Purchase Details Updated Successfully!</span>'));
		return 202;
	}

	public function remove_readd_purchase($action){
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
		if($this->form_validator->run_rules('remove_readd_purchase') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post("user_id");
		$purchase_id = $this->input->post('purchase_id');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		if($user_details->owner_id){
			$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
			if(!$owner_details){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
		}
		$purchase_details = $this->purchases_model->get_purchase_by_id($purchase_id, ($user_details->show_inactive == 0), true);
		if(!$purchase_details || $purchase_details->suspended == 1){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$product_id = $purchase_details->product_id;
		$product_details = $this->products_model->get_product_by_product_id($product_id, false, true);
		if(!$product_details || $product_details->suspended == 1 || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$product = $this->products_model->get_product_with_inventory_details($product_id, false, true);
		$new_level = (int)$product->inventory_level - (int)$purchase_details->quantity;
		switch ($action) {
			case 'disable':
				if($new_level < 0){
					$this->common->set_headers(409);
					echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
					return 409;
				}
				$data1 = array("active" => 0, "modified_by" => $user_id);
				$data2 = array("active" => 0);
				$response1 = $this->purchases_model->disable_enable_purchase($purchase_id, $data1);
				$response2 = $this->payments_model->update_payment($purchase_details->payment_id, $data2);
				if($response1 && $response2){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Purchase Disabled Successfully!</span>'));
					return 202;
				}
				break;
			case 'enable':
				$data1 = array("active" => 1, "modified_by" => $user_id);
				$data2 = array("active" => 1);
				$response1 = $this->purchases_model->disable_enable_purchase($purchase_id, $data1);
				$response2 = $this->payments_model->update_payment($purchase_details->payment_id, $data2);
				if($response1 && $response2){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Purchase Enabled Successfully!</span>'));
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

	public function add_sale(){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		if(sizeof($_POST) < 1 || sizeof($_POST['data']) < 1 || !is_array($_POST['data'])){
			$this->common->set_headers(412);
			echo json_encode(array('status'=>412,'errors'=>'<br><br><span>Ensure All Required Fields are Filled!</span>'));
			return 412;
		}
		$pay_for = $this->payments_model->get_payfor_by_name('sale');
		if(!$pay_for){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		if($this->find_duplicates_in_sale_insert_array($_POST['data'])){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>'<br><br><span>Duplicates Found in Data. A Request Cannot Contain Rows with Duplicate Products!</span>'));
			return 400;
		}
		$data = array();
		foreach ($_POST['data'] as $key => $value) {
			$product_id = $value['product_id'];
			$user_id = $this->input->post('user_id');
			$quantity = $value['quantity'];
			$discount = $value['discount'];
			if(isset($method_id) && $method_id !== $value['method_id']){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			$method_id = $value['method_id'];
			$post_data = array('product_id' => $product_id, 'user_id' => $user_id, 'quantity' => $quantity, 'discount' => $discount, 'method_id' => $method_id);
			if($this->form_validator->run_add_sale_rules($post_data) == false){
				$this->common->set_headers(400);
				echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
				return 400;
			}
			$user_details = $this->users_model->get_user_by_id($user_id);
			if(!$user_details || !strpos(strtolower($user_details->status), "active")){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			if($user_details->owner_id){
				$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
				if(!$owner_details){
					$this->common->set_headers(409);
					echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
					return 409;
				}
			}
			$product_details = $this->products_model->get_product_by_product_id($product_id, true, true);
			if(!$product_details || !strpos(strtolower($product_details->status), "active") || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
			$inventory = $this->products_model->get_product_with_inventory_details($product_id, true, true);
			if(!$inventory || (int)$inventory->inventory_level < (int)$quantity){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Insufficient Inventory Available to Make That Sale!</span>'));
				return 409;
			}
			$method = $this->payments_model->get_payment_method_by_id($method_id);
			if(!$method){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Invalid Payment Method!</span>'));
				return 409;
			}
			$payment_id = $this->payments_model->add_payment(array('payfor_id' => $pay_for->payfor_id, 'method_id' => $method_id));
			if(!$payment_id){
				$this->common->set_headers(500);
				echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Unable to Add Payment. Contact Admin!</span>'));
				return 500;
			}
			$data[] = array('product_id' => $product_id, 'recorded_by' => $user_id, 'modified_by' => $user_id, 'quantity' => $quantity, 'cost_per_item' => $product_details->cost_per_unit, 'discount' => $discount, 'payment_id' => $payment_id);
		}
		if(sizeof($data) < 1){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$response = $this->sales_model->add_sales_batch($data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Sale(s) Recorded Successfully!</span>'));
		return 202;
	}

	public function modify_sale(){
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
		if($this->form_validator->run_rules('edit_sale') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$sale_id = $this->input->post('sale_id');
		$product_id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');
		$quantity = $this->input->post('quantity');
		$cost = $this->input->post('cost_per_item');
		$discount = $this->input->post('discount');
		$method_id = $this->input->post('method_id');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		if($user_details->owner_id){
			$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
			if(!$owner_details){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
		}
		$original_sale = $this->sales_model->get_sale_by_id($sale_id, ($user_details->show_inactive == 0), true);
		if(!$original_sale){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Are NOT Allowed to Update That Sale!</span>'));
			return 409;
		}
		$product_details = $this->products_model->get_product_with_inventory_details($product_id, false, true);
		if(!$product_details || $product_details->suspended == 1 || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$original_product = $this->products_model->get_product_with_inventory_details($original_sale->product_id, false, true);

		if($original_sale->product_id != $product_id){
			$new_level = (int)$product_details->inventory_level - (int)$quantity;
			if($new_level < 0){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
				return 409;
			}
			if($product_details->active != 1){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Add That Product Since It has Been Disabled!</span>'));
				return 409;
			}
		}else{
			$new_level = (int)$original_product->inventory_level + (int)$original_sale->quantity - (int)$quantity;
			if($new_level < 0){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
				return 409;
			}
		}

		$method = $this->payments_model->get_payment_method_by_id($method_id);
		if(!$method){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}

		$data = array('product_id'=>$product_id,'modified_by'=>$user_id,'quantity'=>$quantity,'cost_per_item'=>$cost,'discount'=>$discount);
		$payment_data = array('payment_id' => $original_sale->payment_id, 'method_id' => $method_id);
		$response = $this->sales_model->update_sale($sale_id, $data, $payment_data);
		if(!$response){
			$this->common->set_headers(500);
			echo json_encode(array('status'=>500,'errors'=>'<br><br><span>An Unnexpected Error Occurred. Contact Admin!</span>'));
			return 500;
		}
		$this->common->set_headers(202);
		echo json_encode(array('status'=>202,'response'=>'<br><br><span>Sale Details Updated Successfully!</span>'));
		return 202;
	}

	public function remove_readd_sale($action){
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
		if($this->form_validator->run_rules('remove_readd_sale') == false){
			$this->common->set_headers(400);
			echo json_encode(array('status'=>400,'errors'=>validation_errors('<br><br><span>','</span>')));
			return 400;
		}
		$user_id = $this->input->post("user_id");
		$sale_id = $this->input->post('sale_id');
		$user_details = $this->users_model->get_user_by_id($user_id);
		if(!$user_details || !strpos(strtolower($user_details->status), "active")){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		if($user_details->owner_id){
			$owner_details = $this->owners_model->get_owner_by_id($user_details->owner_id, true, true);
			if(!$owner_details){
				$this->common->set_headers(409);
				echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
				return 409;
			}
		}
		$sale_details = $this->sales_model->get_sale_by_id($sale_id, ($user_details->show_inactive == 0), true);
		if(!$sale_details || $sale_details->suspended == 1){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$product_id = $sale_details->product_id;
		$product_details = $this->products_model->get_product_by_product_id($product_id, false, true);
		if(!$product_details || $product_details->suspended == 1 || ($product_details->owner_id !== $user_details->owner_id && $product_details->owner_id !== $user_details->id_owner)){
			$this->common->set_headers(409);
			echo json_encode(array('status'=>409,'errors'=>'<br><br><span>Data Provided to Server Cannot Be Used to Execute the Desired Functionality!</span>'));
			return 409;
		}
		$product = $this->products_model->get_product_with_inventory_details($product_id, false, true);
		$new_level = (int)$product->inventory_level - (int)$sale_details->quantity;
		switch ($action) {
			case 'disable':
				$data1 = array("active" => 0, "modified_by" => $user_id);
				$data2 = array("active" => 0);
				$response1 = $this->sales_model->disable_enable_sale($sale_id, $data1);
				$response2 = ($response1) ? $this->payments_model->update_payment($sale_details->payment_id, $data2) : false;
				if($response1 && $response2){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Sale Disabled Successfully!</span>'));
					return 202;
				}
				break;
			case 'enable':
				if($new_level < 0){
					$this->common->set_headers(409);
					echo json_encode(array('status'=>409,'errors'=>'<br><br><span>You Cannot Perform that Action Since The Product\'s Inventory Level Will go Below 0.</span>'));
					return 409;
				}
				$data1 = array("active" => 1, "modified_by" => $user_id);
				$data2 = array("active" => 1);
				$response1 = $this->sales_model->disable_enable_sale($sale_id, $data1);
				$response2 = ($response1) ? $this->payments_model->update_payment($sale_details->payment_id, $data2) : false;
				if($response1 && $response2){
					$this->common->set_headers(202);
					echo json_encode(array('status'=>202,'response'=>'<br><br><span>Sale Enabled Successfully!</span>'));
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

	private function find_duplicates_in_sale_insert_array($data){
		$product_ids = array();
		foreach ($data as $value) {
			if(sizeof($product_ids) > 0 && in_array($value['product_id'], $product_ids)) return true;
			array_push($product_ids, $value['product_id']);
		}
		return false;
	}
}