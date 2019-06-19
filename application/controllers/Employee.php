<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {
	private $template = "templates/main/template";
	private $print_table_template = "templates/print/table-template";

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('userdata') || (int)$this->session->userdata('userdata')['level'] > 3){
			redirect(site_url('auth/log_out'),'location');
		}
		//csrfProtector::init();
		$this->load->library('jsons');
	}

	/**
	* Below Here is code to load views. It directly translates to the links in each respective NavigationBar
	*/
	public function index(){
		redirect(site_url('pointofsale/purchases'), 'location');
	}
}