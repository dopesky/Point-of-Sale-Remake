<?php defined('BASEPATH') or die('No direct script access allowed');

class Owners_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function add_owner($data){
		return $this->db->insert('owner',$data) ? $this->db->insert_id() : false;
	}

	function get_owner_employees($user_id, $check_active, $check_employee_suspended = false){
		$this->db->select('employees.*,user_employee.*,departments.department');
		$this->db->where('owner.user_id',$user_id);
		if($check_active){
			$this->db->where('employees.active',1);
		}
		if($check_employee_suspended){
			$this->db->where('user_employee.suspended',0);
		}
		$this->db->where('user_owner.suspended',0);
		$this->db->join('departments','departments.department_id = employees.department_id');
		$this->db->join('owner','owner.owner_id = employees.owner_id');
		$this->db->join('tbl_users as user_owner','user_owner.user_id = owner.user_id');
		$this->db->join('tbl_users as user_employee','user_employee.user_id = employees.user_id');
		return $this->db->get('employees')->result();
	}

	function get_owner_by_user_id($user_id, $check_suspended = true){
		$this->db->where('tbl_users.user_id', $user_id);
		if($check_suspended){
			$this->db->where('tbl_users.suspended',0);
		}
		$this->db->join('tbl_users','tbl_users.user_id = owner.user_id');
		return $this->db->get('owner')->row();
	}
}