<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments_model extends MY_Model {

	public function __construct(){
		parent::__construct();
	}

	public function get_valid_payment_methods(){
		$this->db->where('suspended', 0);
		return $this->db->get('payment_methods')->result();
	}

	public function get_payfor_by_name($name, $check_suspended = true){
		$this->db->where('pay_for', $name);
		if($check_suspended){
			$this->db->where('suspended', 0);
		}
		return $this->db->get('pay_for')->row();
	}

	public function get_payment_method_by_id($id, $check_suspended = true){
		$this->db->where(array('method_id' => $id));
		if($check_suspended){
			$this->db->where('suspended', 0);
		}
		return $this->db->get('payment_methods')->row();
	}

	public function add_payment($payment_data){
		return $this->db->insert('payments', $payment_data) ? $this->db->insert_id() : false;
	}

	public function update_payment($payment_id, $data){
		$this->db->where('payment_id', $payment_id);
		return $this->db->update('payments', $data);
	}
}
