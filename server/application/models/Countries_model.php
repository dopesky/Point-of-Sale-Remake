<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Countries_model extends MY_Model {

	public function __construct(){
		parent::__construct();
	}

	public function get_countries($check_suspended){
		if($check_suspended)
			$this->db->where('suspended', 0);
		$this->db->order_by('country_name', 'ASC');
		return $this->db->get('countries')->result();
	}

	public function get_country_by_name($name, $check_suspended){
		$this->db->where('country_name', $name);
		if($check_suspended)
			$this->db->where('suspended', 0);
		return $this->db->get('countries')->row();
	}
}
