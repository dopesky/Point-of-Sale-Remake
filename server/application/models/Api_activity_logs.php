<?php


class Api_activity_logs extends MY_Model{
	public function __construct(){
		parent::__construct();
	}

	public function add_log($actor_id, $action){
		$data = ['owner_id' => $actor_id, 'action' => $action];
		return $this->db->insert('apiactivitylogs', $data) ? $this->db->insert_id() : false;
	}

	public function get_user_specific_logs($owner_id, $limit = 50, $offset = 0){
		$this->db->where('apiactivitylogs.owner_id', $owner_id);
		$this->db->join('apikey_owners', 'apiactivitylogs.owner_id = apikey_owners.owner_id');
		$this->db->order_by('apiactivitylogs.activitylog_id', 'desc');
		$result = $this->db->get('apiactivitylogs', $limit, $offset)->result_array();
		return (object) array_map(function($log){
			$log['image'] = "https://www.gravatar.com/avatar/".md5(strtolower(trim($log['owner_email'])))."?d=identicon";
			return $log;
		}, $result);
	}
}
