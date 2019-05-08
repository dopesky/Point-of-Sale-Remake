<?php defined('BASEPATH') or die('No direct script access allowed');

class Owners_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function add_owner($data){
		return $this->db->insert('owner',$data) ? $this->db->insert_id() : false;
	}
}