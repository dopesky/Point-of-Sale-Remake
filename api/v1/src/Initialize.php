<?php 
defined('SERVER_URL') or define('SERVER_URL',getenv('SITE_DOMAIN').'server');

function set_options($ch,$apikey,$content_type = 'application/json'){
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'APIKEY: '.$apikey,
      'Content-Type: '.$content_type
   	));
}

function get_response($ch,$response,$return_headers){
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$headers = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	return $return_headers ? $headers : json_decode($body);
}
?>