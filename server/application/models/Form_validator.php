<?php !defined(BASEPATH) or die('No direct script access allowed');

/**
 * This is a model to store all validation rules to be used in this project.
 * NOTE: Do NOT change this to anything other than a model otherwise it will not work.
 */
class Form_validator extends MY_Model {

	//The constructor function
	public function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
	}

	/**
	 * The file that runs validation rules based on what form was submitted.
	 * @return bool - result after running the rules
	 * @var $form - The rules to run / the submitted form
	 */
	public function run_rules($form) {
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
			case 'change_email':
				return $this->run_change_email_rules();
			case 'change_password':
				return $this->run_change_password_rules();
			case 'update_owner':
				return $this->run_update_owner_rules();
			case 'add_product':
				return $this->run_add_product_rules();
			case 'update_product':
				return $this->run_update_product_rules();
			case 'remove_readd_product':
				return $this->run_reactivate_deactivate_product_rules();
			case 'edit_purchase':
				return $this->run_edit_purchase_rules();
			case 'remove_readd_purchase':
				return $this->run_remove_readd_purchase_rules();
			case 'edit_sale':
				return $this->run_edit_sale_rules();
			case 'remove_readd_sale':
				return $this->run_remove_readd_sale_rules();
			case 'change_country':
				return $this->run_change_country_rules();
			case 'update_employee_self':
				return $this->run_update_employee_self_rules();
			default:
				return false;
		}
	}

	//Runs form validation for login functionality
	private function run_login_rules() {
		$this->form_validation->set_rules('email', 'Email', 'trim|strtolower|required|callback_check_email', array('check_email' => "{field} is of Invalid Format!"));
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
		return $this->form_validation->run($this);
	}

	private function run_sign_up_rules() {
		$this->form_validation->set_rules('email', 'Email', 'trim|strtolower|required|callback_check_email|is_unique[tbl_users.email]', array('check_email' => "{field} is of Invalid Format!", 'is_unique' => '{field} has already been registered!'));
		$this->form_validation->set_rules('country', 'Country', 'trim|strtolower|regex_match[/^[a-z ()-]+$/]', array('regex_match' => '{field} has Illegal Characters!'));
		return $this->form_validation->run($this);
	}

	private function run_api_sign_up_rules() {
		$this->form_validation->set_rules('email', 'Email', 'trim|strtolower|required|callback_check_email|is_unique[apikey_owners.owner_email]', array('check_email' => "{field} is of Invalid Format!", 'is_unique' => '{field} has already been registered!'));
		return $this->form_validation->run($this);
	}

	private function run_forgot_password_rules() {
		$this->form_validation->set_rules('email', 'Email', 'trim|strtolower|required|callback_check_email', array('check_email' => "{field} is of Invalid Format!"));
		return $this->form_validation->run($this);
	}

	private function run_password_reset_rules() {
		$this->form_validation->set_rules('new_password', 'New Password', "required|regex_match[/[a-z]/]|regex_match[/[A-Z]/]|regex_match[/[0-9]/]|min_length[8]", array('regex_match' => 'Passwords must have at least one uppercase letter, one lowercase letter and one number'));
		$this->form_validation->set_rules('repeat_password', 'Repeat Password', "required|matches[new_password]", array('matches' => 'Passwords must match.'));
		return $this->form_validation->run($this);
	}

	private function run_sign_up_owner_rules() {
		$this->form_validation->set_rules('first_name', 'First Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('last_name', 'Last Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('company', 'Company', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]|is_unique[owner.company]", array('regex_match' => '{field} Contains Invalid Characters.', 'is_unique' => '{field} has already been registered!'));
		$this->form_validation->set_rules('user_id', 'User ID', "trim|required|regex_match[/^[0-9]+$/]|is_unique[owner.user_id]", array('regex_match' => '{field} Contains Invalid Characters.', 'is_unique' => '{field} has already been used to register an owner!'));
		return $this->form_validation->run($this);
	}

	private function run_update_owner_rules() {
		$this->form_validation->set_rules('first_name', 'First Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('last_name', 'Last Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('company', 'Company', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]|callback_is_company_unique[user_id]", array('regex_match' => '{field} Contains Invalid Characters.', 'is_company_unique' => "{field} Has Already Been Used to Register an Owner!"));
		$this->form_validation->set_rules('user_id', 'User ID', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => '{field} Contains Invalid Characters.'));
		return $this->form_validation->run($this);
	}

	private function run_update_employee_self_rules() {
		$this->form_validation->set_rules('first_name', 'First Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('last_name', 'Last Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('user_id', 'User ID', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => '{field} Contains Invalid Characters.'));
		return $this->form_validation->run($this);
	}

	private function run_add_employee_rules() {
		$this->form_validation->set_rules('first_name', 'First Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('last_name', 'Last Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('email', 'Email', "trim|strtolower|required|callback_check_email|is_unique[tbl_users.email]", array('check_email' => "{field} is of Invalid Format!", 'is_unique' => '{field} has already been registered!'));
		$this->form_validation->set_rules('department_id', 'Department', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_update_employee_rules() {
		$this->form_validation->set_rules('first_name', 'First Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('last_name', 'Last Name', "trim|strtolower|required|regex_match[/^[a-z \'-]+$/i]", array('regex_match' => '{field} Contains Invalid Characters.'));
		$this->form_validation->set_rules('email', 'Email', "trim|strtolower|required|callback_check_email|callback_is_email_unique[employee_id]", array('check_email' => "{field} is of Invalid Format!", 'is_email_unique' => '{field} Has Already Been Registered!'));
		$this->form_validation->set_rules('department_id', 'Department', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('employee_id', 'Employee ID', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_unemploy_reemploy_employee_rules() {
		$this->form_validation->set_rules('employee_id', 'Employee ID', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', 'Owner User ID', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_change_email_rules() {
		$this->form_validation->set_rules('email', 'Email', 'trim|strtolower|required|callback_check_email|callback_is_email_unique[user_id]', array('check_email' => "{field} is of Invalid Format!", 'is_email_unique' => '{field} Has Already Been Registered!'));
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_change_password_rules() {
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
		return $this->run_password_reset_rules();
	}

	private function run_add_product_rules() {
		$this->form_validation->set_rules('product', 'Product Name', "trim|strtolower|required|regex_match[/^[a-z0-9 \'-]+$/i]|callback_is_product_unique[user_id]", array('regex_match' => "{field} Contains Invalid Characters.", 'is_product_unique' => "{field} Has Already Been Used."));
		$this->form_validation->set_rules('category', 'Category', "trim|required|regex_match[/^[0-9]+$/i]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('cost', 'Cost Per Unit', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_update_product_rules() {
		$this->form_validation->set_rules('product', 'Product Name', "trim|strtolower|required|regex_match[/^[a-z0-9 \'-]+$/i]|callback_is_product_unique_update[product_id/user_id]", array('regex_match' => "{field} Contains Invalid Characters.", 'is_product_unique_update' => "{field} Has Already Been Used."));
		$this->form_validation->set_rules('category', 'Category', "trim|required|regex_match[/^[0-9]+$/i]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('cost', 'Cost Per Unit', "trim|required|regex_match[/^[0-9]+$/]", array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_reactivate_deactivate_product_rules() {
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	public function run_add_purchase_rules($data) {
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('quantity', "Quantity", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('total_cost', "Total Cost", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('discount', "Discount", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('method_id', "Payment Method", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_edit_purchase_rules() {
		$this->form_validation->set_rules('purchase_id', "Purchase ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('quantity', "Quantity", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('total_cost', "Total Cost", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('discount', "Discount", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('method_id', "Payment Method", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_remove_readd_purchase_rules() {
		$this->form_validation->set_rules('purchase_id', "Purchase ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	public function run_add_sale_rules($data) {
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('quantity', "Quantity", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('discount', "Discount", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('method_id', "Payment Method", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_edit_sale_rules() {
		$this->form_validation->set_rules('sale_id', "Sale ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('product_id', "Product ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('quantity', "Quantity", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('cost_per_item', "Total Cost", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('discount', "Discount", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('method_id', "Payment Method", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_remove_readd_sale_rules() {
		$this->form_validation->set_rules('sale_id', "Sale ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	private function run_change_country_rules() {
		$this->form_validation->set_rules('country', "Country Name", 'trim|strtolower|required|regex_match[/^[a-z ()-]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		$this->form_validation->set_rules('user_id', "User ID", 'trim|required|regex_match[/^[0-9]+$/]', array('regex_match' => "{field} Contains Invalid Characters."));
		return $this->form_validation->run($this);
	}

	public function is_email_unique($str, $field) {
		$id = $this->input->post($field);
		if ($id === null) return false;
		$this->db->limit(1);
		$this->db->where(array('email' => $str));
		$this->db->group_start();
		$this->db->where(array($field . " != " => $id));
		$this->db->or_where($field . " is null", null, false);
		$this->db->group_end();
		return $this->db->get('user_details')->num_rows() === 0;
	}

	public function is_company_unique($str, $field) {
		$id = $this->input->post($field);
		if ($id === null) return false;
		return ($this->db->limit(1)->get_where('owner', array('company' => $str, $field . " != " => $id))->num_rows() === 0);
	}

	public function is_product_unique($str, $field) {
		$id = $this->input->post($field);
		if ($id === null) return false;
		return ($this->db->limit(1)->get_where('product_details', array('product' => $str, $field => $id))->num_rows() === 0);
	}

	public function is_product_unique_update($str, $field) {
		if (!strpos($field, '/') || sizeof(explode('/', $field)) !== 2) return false;
		$split = explode('/', $field);
		$field1 = $split[0];
		$field2 = $split[1];
		$id = $this->input->post($field1);
		$user_id = $this->input->post($field2);
		if ($id === null) return false;
		return ($this->db->limit(1)->get_where('product_details', array('product' => $str, $field2 => $user_id, $field1 . " != " => $id))->num_rows() === 0);
	}

	//This function checks email to validate that it is a valid email by format not by existence. It is a callback function.
	public function check_email($email) {
		$find1 = strpos($email, '@');
		$find2 = strrpos($email, '.');
		return ($find1 !== false && $find2 !== false && ($find1 + 2) < $find2 && ($find2 + 2) < strlen($email));
	}

	//This function checks email to validate that it is a valid email by format not by existence and allows its length to be 0. It is a callback function.
	public function check_email_allow_0($email) {
		if (strlen($email) === 0) return true;
		return $this->check_email($email);
	}
}
