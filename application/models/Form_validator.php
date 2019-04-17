<?php !defined(BASEPATH) or die('No direct script access allowed');

/**
* This is a model to store all validation rules to be used in this project.
* NOTE: Do NOT change this to anything other than a model otherwise it will not work.
*/
class Form_validator extends CI_Model {

    //The constructor function
  	public function __construct(){
  		parent::__construct();
      $this->load->library('form_validation');
  	}

    /**
    * The file that runs validation rules based on what form was submitted.
    * @var $form - The rules to run / the submitted form
    * @var $required - specify if the form fields are required
    */
  	public function run_rules($form,$required=false){
      switch ($form) {
        case 'login':
          return $this->run_login_rules();
      }
  	}

    //Runs form validation for login functionality
    private function run_login_rules(){
      $this->form_validation->set_rules('username','Email','trim|required|callback_check_email',array('check_email'=>"{field} is of Invalid Format!"));
      $this->form_validation->set_rules('password','Password','required|min_length[8]');
      return $this->form_validation->run($this);
    }

    //This function checks email to validate that it is a valid email by format not by existence. It is a callback function.
  	public function check_email($email){
  		$find1 = strpos($email, '@');
      $find2 = strrpos($email, '.');
      return ($find1 !== false && $find2 !== false && ($find1+2)<$find2 && ($find2+2)<strlen($email));
  	}

    //This function checks email to validate that it is a valid email by format not by existence and allows its length to be 0. It is a callback function.
    public function check_email_allow_0($email){
      if(strlen($email)===0) return true;
      return $this->check_email($email);
    }
}