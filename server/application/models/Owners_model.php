<?php defined('BASEPATH') or die('No direct script access allowed');

class Owners_model extends MY_Model {

	function __construct(){
		parent::__construct();
	}

	function get_owner_by_user_id($user_id, $check_suspended = true){
		$this->db->where('tbl_users.user_id', $user_id);
		if($check_suspended){
			$this->db->where('tbl_users.suspended',0);
			$this->db->where('owner.active',1);
		}
		$this->db->join('tbl_users','tbl_users.user_id = owner.user_id');
		return $this->db->get('owner')->row();
	}

	function get_owner_by_id($owner_id, $check_active, $check_suspended){
		$this->db->where('id_owner', $owner_id);
		if($check_active){
			$this->db->where('owner_active', 1);
		}
		if($check_suspended){
			$this->db->where('suspended', 0);
		}
		return $this->db->get('user_details')->row();
	}

	function get_owner_employees($owner_id, $check_active = false, $check_employee_suspended = false){
		$this->db->select('user_details.*');
		$this->db->where('user_details.owner_id',$owner_id);
		if($check_active){
			$this->db->where('user_details.employee_suspended',0);
		}
		if($check_employee_suspended){
			$this->db->where('user_details.suspended',0);
		}
		$this->db->where('owner.suspended',0);
		$this->db->where('owner.owner_active',1);
		$this->db->join('user_details as owner','owner.id_owner = user_details.owner_id');
		$this->db->order_by("user_details.user_id", "ASC");
		return $this->db->get('user_details')->result();
	}

	function get_owner_products($user_id, $check_active = false, $check_suspended = false){
		$this->db->select('product_details.*');
		$this->db->where('product_details.user_id',$user_id);
		if($check_active){
			$this->db->where('product_details.active',1);
		}
		if($check_suspended){
			$this->db->where('product_details.suspended',0);
		}
		$this->db->where('product_details.owner_suspended',0);
		$this->db->where('product_details.owner_active',1);
		$this->db->order_by("product_details.product_id", "ASC");
		return $this->db->get('product_details')->result();
	}

	function add_owner($data){
		return $this->db->insert('owner',$data) ? $this->db->insert_id() : false;
	}

	function update_owner_by_user_id($user_id, $data){
		$this->db->where("user_id",$user_id);
		return $this->db->update("owner",$data);
	}
}
