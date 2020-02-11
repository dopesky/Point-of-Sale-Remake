<?php defined('BASEPATH') or die('No direct script access allowed');

class Database_timezone_set_model extends MY_Model {

	function __construct() {
		parent::__construct();
		$this->db->query('set time_zone = ?', $this->time->format_date('now', 'P'));
	}
}
