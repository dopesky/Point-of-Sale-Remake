<?php defined('BASEPATH') or die('No direct script access allowed');

class Users_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function get_user_by_email($email){
		return $this->db->where('email',$email)->join('owner','tbl_users.user_id = owner.user_id','left')->get('tbl_users')->row();
	}
}