<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
* This is an Email class that is a child of the codeigniter email class. It is to mitigate the challenges faced by the native
* codeigniter email class by using PHPMailer dependency to send emails. PHPMailer is robust and easy to use. It uses smtp
* to send emails and the smtp credentials are set as environment variables. These can be changed if need be. The credentials 
* have an 'EMAIL_' prefix. PHPMailer is loaded via composer in vendor directory.
*/
class MY_Email extends CI_Email{
	//The constructor function
	public function __construct(){
		parent::__construct();
	}

	//This function returns the main email users can use to reach us with.
	public function get_official_email_address(){
		return 'mail@'.getenv('EMAIL_DOMAIN');
	}

	/**
	* Function to send mail.
	* @var $to - the receiver's email
	* @var $subject - the subject of the email
	* @var $body - the body of the email. i.e the html content to be shown in the email.
	* @var $type - the type of the email. This is appended to the email as $type@example.com
	* @var $name - the name of the recepient. Defaults to user.
	* @var $replyTo - the email to reply to. Defaults to noreply@example.com
	*/
	public function send_email($to,$subject,$body,$type,$name='user',$replyTo='noreply'){
		$mail = new PHPMailer(true);
		try {
		    //$mail->SMTPDebug = 2;
		    $mail->isSMTP();
		    $mail->Host = getenv('EMAIL_HOST');
		    $mail->SMTPAuth = true;
		    $mail->Username = getenv('EMAIL_USERNAME');
		    $mail->Password = getenv('EMAIL_PASSWORD');
		    $mail->SMTPSecure = 'ssl';
		    $mail->Port = 465;
		    $mail->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);
		    $mail->setFrom($type.'@'.getenv('EMAIL_DOMAIN'), "Point of Sale");
		    $mail->addAddress($to,$name);
		    $mail->addReplyTo($replyTo.'@'.getenv('EMAIL_DOMAIN'), "POS");

		    $mail->isHTML(true);
		    $mail->Subject = $subject;
		    $mail->Body    = $body;

		    return $mail->send();
		} catch (Exception $e) {
		    return $mail->ErrorInfo;
		}
	}

	/**
	* This function gets the email html to be passed as $body in the above function(send_email). It first gets the html email 
	* body template from templates/email_templates/ and appends the email message to the email html body.
	*
	* @var $type - the type of email one is sending. e.g intro, password
	* @var $pass - data to send to the email message view. Defaults to an empty array if no data is to be sent.
	* @var $user - the name of the email recipient. Defaults to user.
	* @var $body - the name of the html body view to be used under templates/email_templates/ folder. Defaults to basic_body. Pass empty string 
	*              here to get message only without html body.
	*/
	public function get_email_body($type,$pass=array(),$user='user',$body='basic_body'){
		$views=array('intro'=>'templates/email_templates/introduction_email_message',
			'password'=>'templates/email_templates/password_reset_message','email_update'=>'templates/email_templates/email_update_message');
		$data['user']=$user;
		$data['type']=$views[$type];
		$data['data']=$pass;
		if($body===''){
			return get_instance()->load->view($views[$type],$data,true);
		}
		return get_instance()->load->view('templates/email_templates/'.$body,$data,true);
	}
}
?>