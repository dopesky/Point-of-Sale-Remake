<?php
/**
* Uses the phpdotenv dependency to load the .env file valiables to the system for use within the project. If .env file does 
* not exist it assumes the environment variables in the .env_example are saved on the server environment. Ensure this is so 
* since some core functionalities like email delivery depend on this setting. 
*/
require_once "vendor/autoload.php";
if(file_exists('.env')){
	$dotenv = Dotenv\Dotenv::create(__DIR__);
	$dotenv->load();
}
?>