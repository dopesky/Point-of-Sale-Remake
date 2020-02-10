<?php


class Api_activity_logs extends MY_Model{
	public function __construct(){
		parent::__construct();
	}

	public function add_log($actor_id, $action){
		$data = ['owner_id' => $actor_id, 'action' => $action];
		return $this->db->insert('apiactivitylogs', $data) ? $this->db->insert_id() : false;
	}
}
