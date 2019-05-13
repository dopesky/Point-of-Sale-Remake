<?php
$models = array();
foreach(glob(__DIR__."/src/*.php") as $model){
	$model_path_array = explode('/',$model);
	$model_name = implode('/', $model_path_array);
	require_once $model_name;
}
?>