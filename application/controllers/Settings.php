<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {
	private $template = "templates/main/template";
	private $navbars = array('','navbars/sales_employee_navbar','','','navbars/owner_navbar');

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || (int)$this->session->userdata('userdata')['level'] < 1){
			redirect(site_url('auth/log_out'),'location');
		}
		//csrfProtector::init();
		$this->load->library('jsons');
	}

	public function index(){
		$data['content'] = 'settings';
		$data['navbar'] = $this->navbars[(int)$this->session->userdata('userdata')['level']];
		$data['user_details'] = $this->jsons->get_user_details($this->session->userdata('userdata')['user_id'], false);
		$data['countries'] = $this->jsons->get_valid_countries(false);
		$this->load->view($this->template,$data);
	}

	// The next Logic is implemented in the settings view. The functions each perform a different functionality.
	public function update_owner_details(){
		if(sizeof($_POST)<1) redirect(site_url('owner'),'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$company = $this->input->post('company');
		$photo_details = isset($_FILES['file']) ? $_FILES['file'] : null;
		$user_details = $this->jsons->get_user_details($user_id, false);
		if(!$user_details->id_owner){
			echo json_encode(array('ok'=>false,'code'=>400,'errors'=>'You need to be an Owner to Perform that Action.'));
			return;
		}
		$update = new Setting(getenv('API_KEY'));
		$response = $update->update_owner_details($user_id, $fname, $lname, $company, $photo_details);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['fname'] = $fname;
			$userdata['lname'] = $lname;
			$this->session->set_userdata('userdata', $userdata);
			echo json_encode(array('ok'=>true, 'photo' => $response->photo, 'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
		}
	}

	public function update_employee_details(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$photo_details = isset($_FILES['file']) ? $_FILES['file'] : null;
		$user_details = $this->jsons->get_user_details($user_id, false);
		if(!$user_details->owner_id){
			echo json_encode(array('ok'=>false,'code'=>400,'errors'=>'You need to be an Employee to Perform that Action.'));
			return;
		}
		$update = new Setting(getenv('API_KEY'));
		$response = $update->update_employee_details($user_id, $fname, $lname, $photo_details);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['fname'] = $fname;
			$userdata['lname'] = $lname;
			$this->session->set_userdata('userdata', $userdata);
			echo json_encode(array('ok'=>true, 'photo' => $response->photo, 'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'code'=>$response->status,'errors'=>$response->errors));
		}
	}

	public function change_email(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$reset = new Setting(getenv('API_KEY'));
		$response = $reset->change_email($user_id, $email, $password);
		if($response->status === 202){
			$userdata = $this->session->userdata('userdata');
			$userdata['email'] = trim(strtolower($email));
			$this->session->set_userdata('userdata',$userdata);
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function change_password(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$password = $this->input->post('password');
		$new_pass = $this->input->post('newPass');
		$repeat_pass = $this->input->post('repeatPass');
		$reset = new Setting(getenv('API_KEY'));
		$response = $reset->change_password($user_id, $password, $new_pass, $repeat_pass);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function enable_disable_2FA(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$action = $this->input->post('action');
		$reset = new Setting(getenv('API_KEY'));
		$response = $reset->activate_deactivate_2FA($user_id, $action);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function enable_disable_show_inactive(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$action = $this->input->post('action');
		$reset = new Setting(getenv('API_KEY'));
		$response = $reset->activate_deactivate_show_inactive($user_id, $action);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function enable_disable_show_deleted(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$action = $this->input->post('action');
		$reset = new Setting(getenv('API_KEY'));
		$response = $reset->activate_deactivate_show_deleted($user_id, $action);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}

	public function change_country(){
		if(sizeof($_POST)<1) redirect($this->session->userdata('userdata')['homepage_url'],'location');
		$user_id = $this->session->userdata('userdata')['user_id'];
		$country = $this->input->post('country');
		$country_change = new Setting(getenv('API_KEY'));
		$response = $country_change->change_country($user_id, $country);
		if($response->status === 202){
			echo json_encode(array('ok'=>true,'response'=>$response->response));
		}else{
			echo json_encode(array('ok'=>false,'errors'=>$response->errors));
		}
	}
}