<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apikeys_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	function add_user($data){
		return $this->db->insert('apikey_owners',$data) ? $this->db->insert_id() : false;
	}

	function get_user_by_email($email){
		return $this->db->select('*,apikey_owners.suspended as suspended,apikey_owners.owner_id as owner_id')->where('apikey_owners.owner_email',$email)->join('apikeys','apikey_owners.owner_id = apikeys.owner_id','left')->join('apikeypowers','apikeys.apikeypower_id = apikeypowers.apikeypower_id','left')->get('apikey_owners')->row();
	}

	function get_user_by_id($owner_id){
		return $this->db->select('*,apikey_owners.suspended as suspended,apikey_owners.owner_id as owner_id')->where('apikey_owners.owner_id',$owner_id)->join('apikeys','apikey_owners.owner_id = apikeys.owner_id','left')->join('apikeypowers','apikeys.apikeypower_id = apikeypowers.apikeypower_id','left')->get('apikey_owners')->row();
	}

	function update_user_details($owner_id,$data){
		if(is_array($data) && !array_key_exists('token_expire', $data)) $data['token_expire'] = 3600;
		return $this->db->where('owner_id',$owner_id)->update('apikey_owners',$data);
	}

	function get_user_keys($owner_id,$check_owner_suspended = true,$check_apikey_suspended = true){
		$this->db->select('*,apikey_owners.suspended as suspended,apikey_owners.owner_id as owner_id,apikeys.suspended as key_suspended');
		$this->db->join('apikeys','apikey_owners.owner_id = apikeys.owner_id');
		$this->db->join('apikeypowers','apikeys.apikeypower_id = apikeypowers.apikeypower_id');
		$this->db->where('apikey_owners.owner_id',$owner_id);
		if($check_owner_suspended){
			$this->db->where('apikey_owners.suspended',0);
		}
		if($check_apikey_suspended){
			$this->db->where('apikeys.suspended',0);
		}
		return $this->db->get('apikey_owners')->result();
	}

	function add_api_key($owner_id,$scope,$apikey){
		$data = array('apikey'=>$apikey,'owner_id'=>$owner_id,'apikeypower_id'=>$scope);
		return $this->db->insert('apikeys',$data) ? $this->db->insert_id():false;
	}

	function update_api_key($apikey_id,$data){
		$update = array('apikeypower_id'=>$data['scope']);
		return $this->db->where('apikeys.apikey_id',$apikey_id)->where('apikeys.suspended',0)->update('apikeys',$update);
	}

	function delete_api_key($apikey_id){
		return $this->db->where('apikeys.apikey_id',$apikey_id)->where('apikeys.suspended',0)->update('apikeys',array('apikeys.suspended'=>1));
	}

	function get_api_key_by_id($apikey_id,$check_owner_suspended = true,$check_apikey_suspended = true){
		$this->db->select('*,apikey_owners.suspended as suspended,apikey_owners.owner_id as owner_id,apikeys.suspended as key_suspended');
		$this->db->join('apikey_owners','apikey_owners.owner_id = apikeys.owner_id');
		$this->db->where('apikeys.apikey_id',$apikey_id);
		if($check_owner_suspended){
			$this->db->where('apikey_owners.suspended',0);
		}
		if($check_apikey_suspended){
			$this->db->where('apikeys.suspended',0);
		}
		return $this->db->get('apikeys')->row();
	}

	function get_api_key_powers(){
		return $this->db->get('apikeypowers')->result();
	}

	function get_api_key($api_key){
		$this->db->where('apikeys.apikey',$api_key);
		$this->db->where('apikeys.suspended',0);
		$this->db->join('apikeypowers','apikeys.apikeypower_id = apikeypowers.apikeypower_id');
		return $this->db->get('apikeys')->row();
	}
}
