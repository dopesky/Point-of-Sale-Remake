<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
csrfProtector::init();
require_once('header_scripts.php');
?>

<?php $this->load->view($content);?>

<?php require_once('footer_scripts.php');?>