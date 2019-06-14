<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pointofsale extends CI_Controller {
	private $template = "templates/main/template";
	private $print_table_template = "templates/print/table-template";
	private $navbars = array('','','','','navbars/owner_navbar');

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || (int)$this->session->userdata('userdata')['level'] < 1){
			redirect(site_url('auth/log_out'),'location');
		}
		//csrfProtector::init();
		$this->load->library('jsons');
	}

	/**
	* This class establishes the main functionality of the system ie the point of sale. Access to this class is meant to be free for all unless 
	* in future it is decided that owners can revoke access to this class for their different employees.
	*/
	public function purchases(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;

		$data['content'] = 'purchases';
		$data['navbar'] = $this->navbars[(int)$this->session->userdata('userdata')['level']];

		$data['user_details'] = $user_details;
		$data['payment_methods'] = $this->jsons->get_valid_payment_methods(false);
		$data['products'] = $this->jsons->get_all_products($owner_id, false);
		$this->load->view($this->template,$data);
	}

	public function sales(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;

		$data['content'] = 'sales';
		$data['navbar'] = $this->navbars[(int)$this->session->userdata('userdata')['level']];

		$data['user_details'] = $user_details;
		$data['payment_methods'] = $this->jsons->get_valid_payment_methods(false);
		$data['products'] = $this->jsons->get_all_products($owner_id, false);
		$this->load->view($this->template,$data);
	}

	/**
	 * Below This point are functions that enable the user to make a purchase into the system, update it and disable or reenable it.
	 * One can Also Print and Convert to excel
	 */
	public function get_products_for_purchase(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		return $this->jsons->get_products_for_purchases($owner_id);
	}

	public function get_purchases(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		return $this->jsons->get_purchases_by_owner_id($owner_id);
	}

	public function add_purchases(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$data = $this->input->post('data');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$purchase = new POS(getenv('API_KEY'));
		$response = $purchase->add_purchase($user_id, $data);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function update_purchase(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$purchase_id = $this->input->post('id');
		$product_id = $this->input->post('item1');
		$quantity = $this->input->post('item2');
		$cost = $this->input->post('item3');
		$discount = $this->input->post('item4');
		$method_id = $this->input->post('item5');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$update = new POS(getenv('API_KEY'));
		$response = $update->update_purchase($user_id, $purchase_id, $product_id, $quantity, $cost, $discount, $method_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function disable_enable_purchase(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$purchase_id = $this->input->post('id');
		$action = $this->input->post('action');
		$update = new POS(getenv('API_KEY'));
		$response = $update->disable_enable_purchase($action, $user_id, $purchase_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function print_purchase_details($locale = null){
		$data['locale'] = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$user_details = $this->jsons->get_user_details($user_id, false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		$data['content'] = 'templates/print/manage_purchases';
		$data['data'] = $this->jsons->get_purchases_by_owner_id($owner_id, false);
		$data['user'] = $this->jsons->get_user_details($user_id,false);
		$data['details'] = "Purchase Details";
		return $this->load->view($this->print_table_template,$data);
	}

	public function download_purchase_details_spreadsheet($locale = null){
		$locale = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$user_details = $this->jsons->get_user_details($user_id, false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		$titles = array('Product Name', 'Quantity', 'Total Cost', 'Discount', 'Paid Via', 'Last Modified', 'Status');
		$numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
		$purchase_details = $this->jsons->get_purchases_by_owner_id($owner_id, false);
		$user_details = $this->jsons->get_user_details($user_id, false);
		$data = array();
		$this->load->library('spreadsheets',array('titles' => $titles));
		foreach ($purchase_details as $detail) {
			$data[] = array(
				'product' => ucwords($detail->product),
				'quantity' => ucwords($detail->category_name),
				'total_cost' => $numberFormatter->formatCurrency($detail->total_cost, $user_details->currency_code),
				'discount' => $numberFormatter->formatCurrency($detail->discount, $user_details->currency_code),
				'method' => ucwords($detail->method),
				'modified_date' => $this->time->format_date($detail->modified_date, "d M, Y • h:iA"),
				'status' => $detail->status
			);
		}
		$this->spreadsheets->write_to_excel($data);
		$this->spreadsheets->save(ucwords($user_details->company),'Purchase Details',$user_details->owner_photo);
	}


	/**
	 * Below This point are functions that enable the user to make a sale into the system, update it and disable or reenable it.
	 * One can Also Print and Convert to excel
	 */
	public function get_products_for_sale(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		return $this->jsons->get_products_for_sale($owner_id);
	}

	public function get_sales(){
		$user_details = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		return $this->jsons->get_sales_by_owner_id($owner_id);
	}

	public function add_sales(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$data = $this->input->post('data');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$purchase = new POS(getenv('API_KEY'));
		$response = $purchase->add_sale($user_id, $data);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function update_sale(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$sale_id = $this->input->post('id');
		$product_id = $this->input->post('item1');
		$quantity = $this->input->post('item2');
		$cost = $this->input->post('item3');
		$discount = $this->input->post('item4');
		$method_id = $this->input->post('item5');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$update = new POS(getenv('API_KEY'));
		$response = $update->update_sale($user_id, $sale_id, $product_id, $quantity, $cost, $discount, $method_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function disable_enable_sale(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$sale_id = $this->input->post('id');
		$action = $this->input->post('action');
		$update = new POS(getenv('API_KEY'));
		$response = $update->disable_enable_sale($action, $user_id, $sale_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function print_sale_details($locale = null){
		$data['locale'] = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$user_details = $this->jsons->get_user_details($user_id, false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		$data['content'] = 'templates/print/manage_sales';
		$data['data'] = $this->jsons->get_sales_by_owner_id($owner_id, false);
		$data['user'] = $this->jsons->get_user_details($user_id,false);
		$data['details'] = "Sale Details";
		return $this->load->view($this->print_table_template,$data);
	}

	public function download_sale_details_spreadsheet($locale = null){
		$locale = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$user_details = $this->jsons->get_user_details($user_id, false);
		$owner_id = ($user_details->owner_id) ? $user_details->owner_id : $user_details->id_owner;
		$titles = array('Product Name', 'Quantity', 'Unit Cost', 'Discount', 'Paid Via', 'Last Modified', 'Status');
		$numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
		$sale_details = $this->jsons->get_sales_by_owner_id($owner_id, false);
		$user_details = $this->jsons->get_user_details($user_id, false);
		$data = array();
		$this->load->library('spreadsheets',array('titles' => $titles));
		foreach ($sale_details as $detail) {
			$data[] = array(
				'product' => ucwords($detail->product),
				'quantity' => ucwords($detail->category_name),
				'cost_per_item' => $numberFormatter->formatCurrency($detail->cost_per_item, $user_details->currency_code),
				'discount' => $numberFormatter->formatCurrency($detail->discount, $user_details->currency_code),
				'method' => ucwords($detail->method),
				'modified_date' => $this->time->format_date($detail->modified_date, "d M, Y • h:iA"),
				'status' => $detail->status
			);
		}
		$this->spreadsheets->write_to_excel($data);
		$this->spreadsheets->save(ucwords($user_details->company),'Sale Details',$user_details->owner_photo);
	}
}
