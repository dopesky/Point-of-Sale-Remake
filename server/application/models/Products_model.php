<?php defined('BASEPATH') or die('No direct script access allowed');

class Products_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function get_product_by_product_id($product_id, $check_active = true, $check_suspended = true){
		$this->db->where('product_id',$product_id);
		if($check_suspended){
			$this->db->where('suspended',0);
			$this->db->where('owner_suspended',0);
		}
		if($check_active){
			$this->db->where('active',1);
			$this->db->where('owner_active',1);
		}
		return $this->db->get('product_details')->row();
	}

	function add_product($data){
		return $this->db->insert('products',$data) ? $this->db->insert_id() : false;
	}

	function update_products_by_product_and_user_ids($product_id, $user_id, $data){
		$this->db->where("user_id",$user_id);
		$this->db->where('product_id',$product_id);
		return $this->db->update("product_details",$data);
	}

	function deactivate_product($product_id, $user_id){
		$this->db->where(array('product_id'=>$product_id,'user_id'=>$user_id));
		return $this->db->update('product_details',array('active'=>0));
	}

	function reactivate_product($product_id, $user_id){
		$this->db->where(array('product_id'=>$product_id,'user_id'=>$user_id));
		return $this->db->update('product_details',array('active'=>1));
	}
}