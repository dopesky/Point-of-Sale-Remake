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

	function update_employee_details_by_owner_and_employee_ids($employee_id,$user_id,$owner_id,$data){
		$email = $data['email'];
		$token = $data['token'];
		$old_email = $data['old_email'];
		unset($data['email']);unset($data['token']);unset($data['old_email']);
		$this->db->trans_start();
		if($old_email !== $email){
			$this->users_model->update_user_details($user_id, array('email'=>$email,'password'=>null,'token'=>$token,'suspended'=>1));
		}
		$this->db->where(array('employee_id'=>$employee_id, 'owner_id'=>$owner_id))->update('employees',$data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function get_user_by_employee_id($employee_id){
		$this->db->select('*, tbl_users.user_id as user_id, tbl_users.suspended as suspended');
		$this->db->where('employees.employee_id',$employee_id);
		$this->db->join('employees','tbl_users.user_id = employees.user_id','left');
		return $this->db->get('tbl_users')->row();
	}

	function unemploy_employee($employee_id,$owner_id){
		return $this->db->where(array('employee_id'=>$employee_id,'owner_id'=>$owner_id))->update('employees',array('active'=>0));
	}

	function reemploy_employee($employee_id,$owner_id){
		return $this->db->where(array('employee_id'=>$employee_id,'owner_id'=>$owner_id))->update('employees',array('active'=>1));
	}

	function check_employment_status($employee_user_id){
		$this->db->where('tbl_users.user_id',$employee_user_id);
		$this->db->where('employees.active',1);
		$this->db->join('tbl_users','tbl_users.user_id = employees.user_id');
		return $this->db->get('employees')->num_rows() > 0;
	}
}