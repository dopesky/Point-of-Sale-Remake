<?php !defined(BASEPATH) or die('No direct script access allowed');

/**
* This is a model to store all validation rules to be used in this project.
*/
class Form_Validator {

    //The constructor function
  	public function __construct(){
  		get_instance()->load->library('form_validation');
  	}

    /**
    * The file that runs validation rules based on what form was submitted.
    * NOTE: The rules are only run in production environment. In development and testing environments the function
    * returns true if formdata exists and false if it does not.
    * @var $form - The rules to run / the submitted form
    */
  	public function run_rules($form,$required=false){
      switch ($form) {
        case 'login':
          return $this->run_login_rules();
      }
  	}

    //Runs form validation for login functionality
    private function run_login_rules(){
      get_instance()->form_validation->set_rules('username','Username','trim|required|regex_match[/^[a-z 0-9]*$/i]');
      get_instance()->form_validation->set_rules('password','Password','required|min_length[8]');
      return get_instance()->form_validation->run();
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