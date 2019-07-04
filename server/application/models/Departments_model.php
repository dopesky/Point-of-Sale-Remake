<?php defined('BASEPATH') or die('No direct script access allowed');

class Departments_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function get_valid_departments(){
		return $this->db->where('suspended', 0)->get('departments')->result();
	}

	function get_department_by_id($id){
		return $this->db->where('department_id', $id)->where('suspended', 0)->get('departments')->row();
	}
}