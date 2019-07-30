<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class is the default for uploading any files to cloud e.g cloudinary. It has a fallback to system storage incase cloud 
 * storage fails. It is also meant to handle any transformations required and manipulate the files or prepare them for delivery.
 */
class Media {
	
	private static $target_dir = "point_of_sale/profile_pics/";

	public function __construct() {
		\Cloudinary::config(array( 
		  "cloud_name" => getenv('CLOUDINARY_CLOUD'), 
		  "api_key" => getenv('CLOUDINARY_KEY'), 
		  "api_secret" => getenv('CLOUDINARY_SECRET')
		));
	}

	public function upload_file($file_array, $user_id){
		$validate = $this->validate_file($file_array);
		if(!$validate['ok']) return $validate;
		return $this->upload_to_cloud($file_array, $user_id);
	}

	private function validate_file($file_array){
		if($file_array['size'] > 26214400){
			return array('ok' => false, 'msg' => '<br><br><span>File Size too High. Maximum File Size 25MB.</span>');
		}
		if(strpos(strtolower($file_array['type']), 'image') === false){
			return array('ok' => false, 'msg' => '<br><br><span>Invalid File Type. Please Upload Only Images!</span>');
		}
		return array('ok' => true, 'msg' => '');
	}

	private function upload_to_cloud($file_array, $user_id){
		try{
			$result = \Cloudinary\Uploader::upload($file_array['tmp_name'], array("resource_type" => "auto", "folder" => Media::$target_dir.$user_id."/"));
			if(!array_key_exists('secure_url', $result)) throw new Exception('');
			return array('ok' => true, 'file_name' => $result['secure_url']);
		}catch(Throwable $th){
			return $this->upload_to_device($file_array, $user_id);
		}
	}

	private function upload_to_device($file_array, $user_id){
		if(!is_dir(FCPATH.Media::$target_dir.$user_id."/")) {
            mkdir(FCPATH.Media::$target_dir.$user_id."/", 0777, true);
        }
		$response = copy($file_array['tmp_name'], FCPATH.Media::$target_dir.$user_id."/".$file_array['name']);
        if($response){
        	return array('ok' => true, 'file_name' => base_url(Media::$target_dir.$user_id."/".$file_array['name']));
        }else{
        	return array('ok' => false, 'msg' => '<br><br><span>Unable to Upload File. An Unnexpected Error Occurred. Contact Admin!</span>');
        }
	}
}