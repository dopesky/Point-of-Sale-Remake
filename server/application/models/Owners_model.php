<?php defined('BASEPATH') or die('No direct script access allowed');

class Owners_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function add_owner($data){
		return $this->db->insert('owner',$data) ? $this->db->insert_id() : false;
	}

	function get_owner_employees($owner_id, $check_active, $check_employee_suspended = false){
		$this->db->select('user_details.*');
		$this->db->where('user_details.owner_id',$owner_id);
		if($check_active){
			$this->db->where('user_details.active',1);
		}
		if($check_employee_suspended){
			$this->db->where('user_details.suspended',0);
		}
		$this->db->where('owner.suspended',0);
		$this->db->join('user_details as owner','owner.id_owner = user_details.owner_id');
		return $this->db->get('user_details')->result();
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