<?php

class POS{

	private $API_KEY = null;

	public function __construct($apikey = ''){
		$this->API_KEY = $apikey;
	}

	public function add_purchase($user_id,$data,$return_headers = false){
		$data = http_build_query(array('data'=>$data,'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL.'/pos/add_purchase');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_purchase($user_id, $purchase_id, $product_id, $quantity, $cost, $discount, $method_id, $return_headers = false){
		$data = http_build_query(array('purchase_id' => $purchase_id, 'user_id' => $user_id, 'product_id' => $product_id, 'quantity' => $quantity, 'total_cost' => $cost, 'method_id' => $method_id, 'discount' => $discount));
		$curl = curl_init(SERVER_URL.'/pos/modify_purchase');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function disable_enable_purchase($action, $user_id, $purchase_id, $return_headers = false){
		$data = http_build_query(array('purchase_id' => $purchase_id, 'user_id' => $user_id));
		$curl = curl_init(SERVER_URL."/pos/remove_readd_purchase/$action");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function add_sale($user_id,$data,$return_headers = false){
		$data = http_build_query(array('data'=>$data,'user_id'=>$user_id));
		$curl = curl_init(SERVER_URL.'/pos/add_sale');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function update_sale($user_id, $sale_id, $product_id, $quantity, $cost, $discount, $method_id, $return_headers = false){
		$data = http_build_query(array('sale_id' => $sale_id, 'user_id' => $user_id, 'product_id' => $product_id, 'quantity' => $quantity, 'cost_per_item' => $cost, 'method_id' => $method_id, 'discount' => $discount));
		$curl = curl_init(SERVER_URL.'/pos/modify_sale');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		set_options($curl,$this->API_KEY,'application/x-www-form-urlencoded');
		$result = curl_exec($curl);
		$response = get_response($curl,$result,$return_headers);
		curl_close($curl);
		return $response;
	}

	public function disable_enable_sale($action, $user_id, $sale_id, $return_headers = false){
		$data = http_build_query(array('sale_id' => $sale_id, 'user_id' => $user_id));
		$curl = curl_init(SERVER_URL."/pos/remove_readd_sale/$action");
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