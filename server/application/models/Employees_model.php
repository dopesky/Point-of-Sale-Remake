<?php defined('BASEPATH') or die('No direct script access allowed');

class Employees_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function add_employee($data){
		$email = $data['email'];
		$token = $data['token'];
		unset($data['email']);unset($data['token']);
		$this->db->trans_start();
		$data['user_id'] = $this->users_model->add_user(array('email'=>$email,'token'=>$token));
		$this->db->insert('employees',$data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function update_employee_details_by_owner_and_employee_ids($employee_id, $employee_user_id, $owner_id,$data){
		$email = $data['email'];
		$token = $data['token'];
		$old_email = $data['old_email'];
		unset($data['email']);unset($data['token']);unset($data['old_email']);
		$this->db->trans_start();
		if($old_email !== $email){
			$this->users_model->update_user_details($employee_user_id, array('email'=>$email,'password'=>null,'token'=>$token,'suspended'=>1));
		}
		$this->db->where(array('employee_id'=>$employee_id, 'owner_id'=>$owner_id))->update('user_details',$data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function get_user_by_employee_id($employee_id, $check_active = false, $check_suspended = false){
		$this->db->where('user_details.employee_id',$employee_id);
		if($check_active){
			$this->db->where('user_details.active',1);
		}
		if($check_suspended){
			$this->db->where('user_details.employee_suspended',0);
			$this->db->where('user_details.suspended',0);
		}
		return $this->db->get('user_details')->row();
	}

	function unemploy_employee($employee_id,$owner_id){
		return $this->db->where(array('employee_id'=>$employee_id,'owner_id'=>$owner_id))->update('employees',array('suspended'=>1));
	}

	function reemploy_employee($employee_id,$owner_id){
		return $this->db->where(array('employee_id'=>$employee_id,'owner_id'=>$owner_id))->update('employees',array('suspended'=>0));
	}

	function update_employee_by_user_id($user_id, $data){
		$this->db->where("user_id", $user_id);
		$this->db->where("employee_suspended", 0);
		$this->db->where("suspended", 0);
		return $this->db->update("user_details",$data);
	}
}