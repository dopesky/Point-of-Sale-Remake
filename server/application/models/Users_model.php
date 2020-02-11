<?php defined('BASEPATH') or die('No direct script access allowed');

class Users_model extends MY_Model {

	function __construct(){
		parent::__construct();
	}

	function get_user_by_email($email){
		return $this->db->where('email',$email)->get('user_details')->row();
	}

	function get_user_by_id($user_id){
		return $this->db->where('user_id',$user_id)->get('user_details')->row();
	}

	function add_user($data){
		if(is_array($data) && !array_key_exists('token_expire', $data)) $data['token_expire'] = 3600;
		return $this->db->insert('tbl_users',$data) ? $this->db->insert_id() : false;
	}

	function update_user_details($user_id,$data){
		if(is_array($data) && !array_key_exists('token_expire', $data)) $data['token_expire'] = 3600;
		return $this->db->where('user_id',$user_id)->update('tbl_users',$data);
	}
}
