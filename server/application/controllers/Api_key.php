<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_key extends MY_Controller {
	private $template = "templates/main/template";

	public function __construct() {
		parent::__construct();
		$this->load->library('email');
		$this->load->library('session');
	}

	public function index() {
		$data['powers'] = $this->apikeys_model->get_api_key_powers();
		$data['content'] = 'apikeys';
		$this->load->view($this->template, $data);
	}

	public function register() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if (!$this->form_validator->run_rules('api_sign_up')) {
			echo json_encode(array('ok' => false, 'errors' => validation_errors('<br><br><span>', '</span>')));
			return false;
		}
		$token = $this->common->get_crypto_safe_token(random_int(25, 30));
		$sign_up_response = $this->apikeys_model->add_user(array('owner_email' => $this->input->post('email'), 'token' => $token));
		if (!$sign_up_response) {
			echo json_encode(array('ok' => false, 'errors' => "<br><br><span>Sign up failed. Please try again!</span>"));
			return false;
		}
		$email_body = $this->email->get_email_body('intro', array('token_url' => site_url('api_key/reset_password/' . $token . "/" . $sign_up_response)));
		$email_response = $this->email->send_email($this->input->post('email'), 'Welcome to POS - API Keys', $email_body, 'introduction');
		if ($email_response === true) {
			$this->session->set_flashdata('info', "<br><br><span>Registration successful. Check your email for more information.</span>");
			echo json_encode(array('ok' => true));
			return true;
		} else {
			echo json_encode(array('ok' => false, 'errors' => "<br><br><span>Email Verification Failed. Try Resetting your Password. If Problem Persists Contact Admin!</span>"));
			return false;
		}
	}

	public function send_reset_email() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if ($this->form_validator->run_rules('forgot_password') == false) {
			echo json_encode(array('ok' => false, 'errors' => validation_errors('<br><br><span>', '</span>')));
			return false;
		}
		$user = $this->apikeys_model->get_user_by_email($this->input->post('email'));
		if (!$user || ($user->suspended == 1 && $user->owner_password)) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>Email not Registered on the System!</span>'));
			return false;
		}
		$token = $this->common->get_crypto_safe_token(random_int(25, 30));
		$resave_token = $this->apikeys_model->update_user_details($user->owner_id, array('token' => $token));
		if (!$resave_token) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>An Unexpected Error Occurred. Contact Admin!</span>'));
			return false;
		}
		$email_body = $this->email->get_email_body('password', array('token_url' => site_url('api_key/reset_password/' . $token . "/" . $user->owner_id)));
		$email_response = $this->email->send_email($user->owner_email, 'Password Reset - API Keys', $email_body, 'password-reset');
		if ($email_response === true) {
			$this->session->set_flashdata('info', "<br><br><span>Password Reset Successful. Check your email for more information.</span>");
			echo json_encode(array('ok' => true));
			return true;
		} else {
			echo json_encode(array('ok' => false, 'errors' => "<br><br><span>Password Reset Failed. Try Again!</span>"));
			return false;
		}
	}

	public function reset_password($token, $id) {
		$verification = $this->apikeys_model->get_user_by_id($id);
		if (!$verification || ($verification->suspended == 1 && $verification->owner_password)) {
			$this->session->set_flashdata('info', "<br><br><span>User <b>NOT</b> Registered in the System. Please Register First!</span>");
			redirect(base_url(), 'location');
			return false;
		}
		if ($token !== $verification->token) {
			$this->session->set_flashdata('info', "<br><br><span>Invalid token supplied.</span>");
			redirect(base_url(), 'location');
			return false;
		}
		$time_to_expire = $this->time->add_time($verification->token_expire, $verification->last_access_time);
		if ($this->time->diff_date($this->time->get_now(), $time_to_expire) > 0) {
			$this->session->set_flashdata('info', "<br><br><span>Reset Token has Expired. Please Request for Another Token.</span>");
			redirect(base_url(), 'location');
			return false;
		}

		if ($this->form_validator->run_rules('password_reset') == false) {
			$data['errors'] = validation_errors('<br><br><span>', '</span>');
			$data['content'] = 'password_reset';
			$this->load->view($this->template, $data);
			return false;
		}
		$data = array('owner_password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT), 'suspended' => 0, 'token_expire' => 0);
		$database_response = $this->apikeys_model->update_user_details($id, $data);
		if ($database_response) {
			$this->session->set_flashdata('info', "<br><br><span>Password Reset Successful! Now Log in!</span>");
			redirect(base_url(), 'location');
			return true;
		} else {
			$this->session->set_flashdata('info', "<br><br><span>Password Reset failed. Try again!</span>");
			redirect(base_url(), 'location');
			return false;
		}
	}

	public function login() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if ($this->form_validator->run_rules('login') == false) {
			echo json_encode(array('ok' => false, 'errors' => validation_errors('<br><br><span>', '</span>')));
			return false;
		}
		$login_credentials = $this->apikeys_model->get_user_by_email($this->input->post('email'));
		if ($login_credentials) {
			if (password_verify($this->input->post('password'), $login_credentials->owner_password) && $login_credentials->suspended == 0) {
				$this->apikeys_model->update_user_details($login_credentials->owner_id, array('token_expire' => 0, 'last_access_time' => $this->time->get_now()));
				echo json_encode(array('ok' => true, 'userdata' => $login_credentials));
				return true;
			} elseif (password_verify($this->input->post('password'), $login_credentials->owner_password)) {
				echo json_encode(array('ok' => false, 'errors' => '<br><br><span>Account has been Suspended!</span>'));
				return false;
			}
		}
		echo json_encode(array('ok' => false, 'errors' => '<br><br><span>Invalid Email or Password!</span>'));
		return false;
	}

	public function generate_api_key() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if (!isset($_POST['owner_id']) || !isset($_POST['scope'])) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>User ID and Scope are Required to Generate API Keys!</span>'));
			return false;
		}
		$apikey = $this->common->get_crypto_safe_token(60);
		$database_response = $this->apikeys_model->add_api_key($_POST['owner_id'], $_POST['scope'], $apikey);
		if ($database_response) {
			return $this->get_api_keys();
		} else {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>An Unexpected Error Occurred!</span>'));
			return false;
		}
	}

	public function update_api_key() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if (!isset($_POST['apikey_id']) || !isset($_POST['scope'])) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>Key ID and Scope are Required to Update API Keys!</span>'));
			return false;
		}
		$database_response = $this->apikeys_model->update_api_key($_POST['apikey_id'], array('scope' => $_POST['scope']));
		if ($database_response) {
			$_POST['owner_id'] = $this->apikeys_model->get_api_key_by_id($_POST['apikey_id'])->owner_id;
			return $this->get_api_keys();
		} else {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>An Unexpected Error Occurred!</span>'));
			return false;
		}
	}

	public function delete_api_key() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if (!isset($_POST['apikey_id'])) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>Key ID is Required to Delete API Keys!</span>'));
			return false;
		}
		$database_response = $this->apikeys_model->delete_api_key($_POST['apikey_id']);
		if ($database_response) {
			$_POST['owner_id'] = $this->apikeys_model->get_api_key_by_id($_POST['apikey_id'])->owner_id;
			return $this->get_api_keys();
		} else {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>An Unexpected Error Occurred!</span>'));
			return false;
		}
	}

	public function get_api_keys() {
		if (sizeof($_POST) < 1) redirect(base_url(), 'location');
		if (!isset($_POST['owner_id'])) {
			echo json_encode(array('ok' => false, 'errors' => '<br><br><span>User ID is Required to Fetch API Keys!</span>'));
			return false;
		}
		$owner_keys = $this->apikeys_model->get_user_keys($this->input->post('owner_id'));
		echo json_encode(array('ok' => true, 'keys' => $owner_keys));
		return true;
	}
}
