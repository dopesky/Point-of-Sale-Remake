<!DOCTYPE html>
<html lang="en">
<head>
	<title>HCI-POS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--Load dependencies from CDNs. These are only for the purpose of front-end design (bootstrap) and validation (JQuery).-->
	<link rel="shortcut icon" type="image/png" href="">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/main_css.css')?>">

	<script>
		//This part contains project constants to be used in js files
		const base_url = "<?=base_url()?>"
	</script>
	<script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>
	<script src='https://ajax.googleapis.com/ajax/libs/angularjs/1.7.7/angular.min.js'></script>
	<script src="<?=base_url('assets/js/main_js.js')?>"></script>
	<!--End of Dependencies. Comment out the above lines to remove any styling and front-end validation used. Note: Do not remove jquery and angularjs-->
</head>
<body>
	<?php
		if(isset($navbar)) $this->load->view($navbar);
	?>
	<div class="container-fluid">