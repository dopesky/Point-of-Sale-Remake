<?php defined('BASEPATH') or die('No direct script access allowed');

class Users_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function get_user_by_email($email){
		return $this->db->select('*,tbl_users.user_id as user_id,tbl_users.suspended as suspended')->where('email',$email)->join('owner','tbl_users.user_id = owner.user_id','left')->get('tbl_users')->row();
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