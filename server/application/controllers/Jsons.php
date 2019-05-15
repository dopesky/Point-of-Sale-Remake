<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jsons extends CI_Controller {

	private $api_key;

	public function __construct(){
		parent::__construct();
		$this->api_key = $this->apikeys_model->get_api_key($_SERVER['HTTP_APIKEY']);
		if(!$this->api_key){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid API Key Provided. Contact Admin!</span>'));
			exit;
		}
	}

	public function get_valid_departments(){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('READ','BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$departments = $this->departments_model->get_valid_departments();
		if($departments){
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>$departments));
			return 202;
		}
		$this->common->set_headers(500);
		echo json_encode(array('status'=>500,'errors'=>'<br><br><span>Unfortunately, No Departments Exist on Site or An Unnexpected Error Occurred. Contact Admin!</span>'));
		return 500;
	}

	public function get_employees_by_owner_user_id($user_id, $check_suspended){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('READ','BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$owner_id = $this->users_model->get_user_by_id($user_id)->id_owner;
		if(!$owner_id){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>Invalid Owner User ID Provided. User Does Not Exist. Contact Admin!</span>'));
			return 403;
		}
		$employees = $this->owners_model->get_owner_employees($owner_id, $check_suspended);
		if($employees){
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>$employees));
			return 202;
		}
		$this->common->set_headers(500);
		echo json_encode(array('status'=>500,'errors'=>'<br><br><span> Either You Have No Employees or An Unnexpected Error Occurred. Contact Admin If You Previously Added Employees!</span>'));
		return 500;
	}

	public function get_user_details($user_id){
		if(!$this->common->check_api_key_power($this->api_key->apikey_power,array('READ','BOTH'))){
			$this->common->set_headers(403);
			echo json_encode(array('status'=>403,'errors'=>'<br><br><span>You do not Have Authorisation to Perform This Action. Contact Admin!</span>'));
			return 403;
		}
		$details = $this->users_model->get_user_by_id($user_id);
		if($details){
			$this->common->set_headers(202);
			echo json_encode(array('status'=>202,'response'=>$details));
			return 202;
		}
		$this->common->set_headers(500);
		echo json_encode(array('status'=>500,'errors'=>'<br><br><span> An Unnexpected Error Occured!</span>'));
		return 500;
	}
}