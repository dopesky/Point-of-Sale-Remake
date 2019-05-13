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
        case 'sign_up':
          return $this->run_sign_up_rules();
        case 'forgot_password':
          return $this->run_forgot_password_rules();
        case 'password_reset':
          return $this->run_password_reset_rules();
        case 'api_sign_up':
          return $this->run_api_sign_up_rules();
        case 'sign_up_owner':
          return $this->run_sign_up_owner_rules();
        case 'add_employee':
          return $this->run_add_employee_rules();
        case 'update_employee':
          return $this->run_update_employee_rules();
        case 'unemploy_reemploy_employee':
          return $this->run_unemploy_reemploy_employee_rules();
      }
  	}

    //Runs form validation for login functionality
    private function run_login_rules(){
      $this->form_validation->set_rules('email','Email','trim|strtolower|required|callback_check_email',array('check_email'=>"{field} is of Invalid Format!"));
      $this->form_validation->set_rules('password','Password','required|min_length[8]');
      return $this->form_validation->run($this);
    }

    private function run_sign_up_rules(){
      $this->form_validation->set_rules('email','Email','trim|strtolower|required|callback_check_email|is_unique[tbl_users.email]',array('check_email'=>"{field} is of Invalid Format!",'is_unique'=> '{field} has already been registered!'));
      return $this->form_validation->run($this);
    }

    private function run_api_sign_up_rules(){
      $this->form_validation->set_rules('email','Email','trim|strtolower|required|callback_check_email|is_unique[apikey_owners.owner_email]',array('check_email'=>"{field} is of Invalid Format!",'is_unique'=> '{field} has already been registered!'));
      return $this->form_validation->run($this);
    }

    private function run_forgot_password_rules(){
      $this->form_validation->set_rules('email','Email','trim|strtolower|required|callback_check_email',array('check_email'=>"{field} is of Invalid Format!"));
      return $this->form_validation->run($this);
    }

    private function run_password_reset_rules(){
      $this->form_validation->set_rules('new_password','New Password',"required|regex_match[/[a-z]/]|regex_match[/[A-Z]/]|regex_match[/[0-9]/]|min_length[8]",array('regex_match'=>'Passwords must have atleast one uppercase letter, one lowercase letter and one number'));
      $this->form_validation->set_rules('repeat_password','Repeat Password',"required|matches[new_password]",array('matches'=>'Passwords must match.'));
      return $this->form_validation->run($this);
    }

    private function run_sign_up_owner_rules(){
      $this->form_validation->set_rules('first_name','First Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('last_name','Last Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('company','Company',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]|is_unique[owner.company]",array('regex_match'=>'{field} Contains Invalid Characters.','is_unique'=> '{field} has already been registered!'));
      $this->form_validation->set_rules('user_id','User ID',"trim|required|regex_match[/^[0-9]+$/]|is_unique[owner.user_id]",array('regex_match'=>'{field} Contains Invalid Characters.','is_unique'=> '{field} has already been used to register an owner!'));
      return $this->form_validation->run($this);
    }

    private function run_add_employee_rules(){
      $this->form_validation->set_rules('first_name','First Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('last_name','Last Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('email','Email',"trim|strtolower|required|callback_check_email|is_unique[tbl_users.email]",array('check_email'=>"{field} is of Invalid Format!",'is_unique'=> '{field} has already been registered!'));
      $this->form_validation->set_rules('department_id','Department',"trim|required|regex_match[/^[0-9]+$/]",array('regex_match'=>"{field} Contains Invalid Characters."));
      return $this->form_validation->run($this);
    }

    private function run_update_employee_rules(){
      $this->form_validation->set_rules('first_name','First Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('last_name','Last Name',"trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]",array('regex_match'=>'{field} Contains Invalid Characters.'));
      $this->form_validation->set_rules('email','Email',"trim|strtolower|required|callback_check_email|callback_is_employee_email_unique[employee_id]",array('check_email'=>"{field} is of Invalid Format!",'is_employee_email_unique'=>'{field} Has Already Been Registered!'));
      $this->form_validation->set_rules('department_id','Department',"trim|required|regex_match[/^[0-9]+$/]",array('regex_match'=>"{field} Contains Invalid Characters."));
      $this->form_validation->set_rules('employee_id','Employee ID',"trim|required|regex_match[/^[0-9]+$/]",array('regex_match'=>"{field} Contains Invalid Characters."));
      return $this->form_validation->run($this);
    }

    private function run_unemploy_reemploy_employee_rules(){
        $this->form_validation->set_rules('employee_id','Employee ID',"trim|required|regex_match[/^[0-9]+$/]",array('regex_match'=>"{field} Contains Invalid Characters."));
         $this->form_validation->set_rules('user_id','Owner User ID',"trim|required|regex_match[/^[0-9]+$/]",array('regex_match'=>"{field} Contains Invalid Characters."));
        return $this->form_validation->run($this);
    }

    public function is_employee_email_unique($str,$field){
      $id = $this->input->post($field);
      if($id === null) return false;
      return ($this->db->limit(1)->join('employees','employees.user_id = tbl_users.user_id')->get_where('tbl_users', array('tbl_users.email' => $str, "employees.".$field." !=" => $id))->num_rows() === 0);
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