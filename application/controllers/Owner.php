<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {
	private $template = "templates/main/template";
	private $print_table_template = "templates/print/table-template";

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || (int)$this->session->userdata('userdata')['level'] < 4){
			redirect(site_url('auth/log_out'),'location');
		}
		//csrfProtector::init();
		$this->load->library('jsons');
	}

	/**
	* Below Here is code to load views. It directly translates to the links in each respective NavigationBar
	*/
	public function index(){
		if($this->session->userdata('userdata')['fname']){
			$data['content'] = 'owner_dashboard';
			$data['navbar'] = 'navbars/owner_navbar';
			$this->load->view($this->template,$data);
		}else{
			$data['content'] = 'owner_registration';
			$this->load->view($this->template,$data);
		}
	}

	public function manage_employees(){
		$data['content'] = 'manage_employees';
		$data['navbar'] = 'navbars/owner_navbar';
		$data['departments'] = $this->jsons->get_valid_departments_json(false);
		$this->load->view($this->template,$data);
	}

	public function manage_products(){
		$data['content'] = 'manage_products';
		$data['navbar'] = 'navbars/owner_navbar';
		$data['categories'] = $this->jsons->get_valid_categories_json(false);
		$data['user_details'] = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false); 
		$this->load->view($this->template,$data);
	}

	public function owner_settings(){
		$data['content'] = 'owner_settings';
		$data['navbar'] = 'navbars/owner_navbar';
		$data['user_details'] = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$this->load->view($this->template,$data);
	}

	/**
	* Below Here lies the logic for every owner of a company in the system.
	* This code connects to the server through the API.
	*/

	// Here Lies code to finish registration of owners by adding their names and company name
	public function register_owner(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$company = $this->input->post('company');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$register = new Owners(getenv('API_KEY'));
		$response = $register->finish_registration($user_id,$fname,$lname,$company);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['fname'] = trim(strtolower($fname));
			$userdata['lname'] = trim(strtolower($lname));
			$this->session->set_userdata('userdata',$userdata);
			$this->session->set_flashdata('info',$response->response);
			echo json_encode(array('ok'=>true));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	// The next Logic is implemented in the manage Employees view. As seen from function names, All CRUD operations are done
	public function get_employees($user_id){
		if($user_id !== $this->session->userdata('userdata')['user_id']){
			echo json_encode(array());
			return array();
		}
		return $this->jsons->get_employees_for_owner($user_id);
	}

	public function employ(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$department = $this->input->post('department');
		$email = $this->input->post('email');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$employ = new Owners(getenv('API_KEY'));
		$response = $employ->employ($user_id,$fname,$lname,$email,$department);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function update_employee_details(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$department = $this->input->post('department');
		$email = $this->input->post('email');
		$employee_id = $this->input->post('id');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$update = new Owners(getenv('API_KEY'));
		$response = $update->update_employee_details($user_id,$employee_id,$fname,$lname,$email,$department);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
		}
	}

	public function unemploy_reemploy_employee(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$employee_id = $this->input->post('id');
		$action = $this->input->post('action');
		$update = new Owners(getenv('API_KEY'));
		$response = $update->unemploy_reemploy_employee($action, $user_id, $employee_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function print_employee_details($user_id){
		if($user_id !== $this->session->userdata('userdata')['user_id']) return;
		$data['content'] = 'templates/print/manage_employees';
		$data['data'] = $this->jsons->get_employees_for_owner($user_id, false);
		$data['user'] = $this->jsons->get_user_details($user_id,false);
		$data['details'] = "Employee Details";
		return $this->load->view($this->print_table_template,$data);
	}

	public function download_employee_details_spreadsheet($user_id){
		if($user_id !== $this->session->userdata('userdata')['user_id']) return;
		$titles = array('Full Name', 'Department', 'Email', 'Status', 'Last Interaction');
		$employee_details = $this->jsons->get_employees_for_owner($user_id, false);
		$user_details = $this->jsons->get_user_details($user_id, false);
		$data = array();
		$this->load->library('spreadsheets',array('titles' => $titles));
		foreach ($employee_details as $detail) {
			$data[] = array(
				'full_name' => ucwords($detail->full_name),
				'department' => ucwords($detail->department),
				'email' => $detail->email,
				'status' => $detail->status,
				'last_access_time' => $this->time->format_date($detail->last_access_time, "d M, Y • h:iA")
			);
		}
		$this->spreadsheets->write_to_excel($data);
		$this->spreadsheets->save(ucwords($user_details->company),'Employee Details',$user_details->owner_photo);
	}

	// The next Logic is implemented in the manage Products view. As seen from function names, All CRUD operations are done
	public function get_products(){
		return $this->jsons->get_products_for_owner($this->session->userdata('userdata')['user_id']);
	}

	public function add_product(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$product = $this->input->post('item1');
		$cost = $this->input->post('item2');
		$category = $this->input->post('item3');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$employ = new Owners(getenv('API_KEY'));
		$response = $employ->register_product($user_id, $product, $category, $cost);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function update_product_details(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$product = $this->input->post('item1');
		$cost = $this->input->post('item2');
		$category = $this->input->post('item3');
		$product_id = $this->input->post('id');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$update = new Owners(getenv('API_KEY'));
		$response = $update->update_product_details($user_id, $product, $category, $cost, $product_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false, 'errors'=>$response->errors));
		}
	}

	public function activate_deactivate_product(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$product_id = $this->input->post('id');
		$action = $this->input->post('action');
		$update = new Owners(getenv('API_KEY'));
		$response = $update->activate_deactivate_product($action, $user_id, $product_id);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function print_product_details($locale = null){
		$data['locale'] = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$data['content'] = 'templates/print/manage_products';
		$data['data'] = $this->jsons->get_products_for_owner($user_id, false);
		$data['user'] = $this->jsons->get_user_details($user_id,false);
		$data['details'] = "Product Details";
		return $this->load->view($this->print_table_template,$data);
	}

	public function download_product_details_spreadsheet($locale = null){
		$locale = !$locale ? "en_US" : $locale;
		$user_id = $this->session->userdata('userdata')['user_id'];
		$titles = array('Product Name', 'Category', 'Cost', 'Status', 'Last Change');
		$numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
		$product_details = $this->jsons->get_products_for_owner($user_id, false);
		$user_details = $this->jsons->get_user_details($user_id, false);
		$data = array();
		$this->load->library('spreadsheets',array('titles' => $titles));
		foreach ($product_details as $detail) {
			$data[] = array(
				'product' => ucwords($detail->product),
				'category_name' => ucwords($detail->category_name),
				'cost' => $numberFormatter->formatCurrency($detail->cost_per_unit, $user_details->currency_code),
				'status' => $detail->status,
				'modified_date' => $this->time->format_date($detail->modified_date, "d M, Y • h:iA")
			);
		}
		$this->spreadsheets->write_to_excel($data);
		$this->spreadsheets->save(ucwords($user_details->company),'Product Details',$user_details->owner_photo);
	}

	// The next Logic is implemented in the settings view. The functions each perform a different functionality.
	public function update_owner_details(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$company = $this->input->post('company');
		$photo_details = isset($_FILES['file']) ? $_FILES['file'] : null;
		$update = new Owners(getenv('API_KEY'));
		$response = $update->update_owner_details($user_id, $fname, $lname, $company, $photo_details);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['fname'] = $fname;
			$userdata['lname'] = $lname;
			$this->session->set_userdata('userdata', $userdata);
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
		}
	}

	public function change_owner_email(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$reset = new Owners(getenv('API_KEY'));
		$response = $reset->change_owner_email($user_id, $email, $password);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['email'] = trim(strtolower($email));
			$this->session->set_userdata('userdata',$userdata);
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function change_owner_password(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$password = $this->input->post('password');
		$new_pass = $this->input->post('newPass');
		$repeat_pass = $this->input->post('repeatPass');
		$reset = new Owners(getenv('API_KEY'));
		$response = $reset->change_owner_password($user_id, $password, $new_pass, $repeat_pass);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function enable_disable_2FA(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$action = $this->input->post('action');
		$reset = new Owners(getenv('API_KEY'));
		$response = $reset->activate_deactivate_2FA($user_id, $action);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}
}
