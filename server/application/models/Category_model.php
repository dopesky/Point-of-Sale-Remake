<?php defined('BASEPATH') or die('No direct script access allowed');

class Category_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function get_valid_categories(){
		return $this->db->where('suspended',0)->get('categories')->result();
	}
}