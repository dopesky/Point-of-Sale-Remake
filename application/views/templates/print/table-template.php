<!DOCTYPE html>
<html lang="en">
<head>
	<title></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--Load dependencies from CDNs. These are only for the purpose of front-end design (bootstrap)-->
	<link rel="shortcut icon" type="image/png" href="<?=base_url('assets/img/favicon.png')?>">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/main_css.css')?>">
	<!--End of Dependencies. Comment out the above lines to remove any styling used.-->
</head>
<body class="print-background">
	<div class="container-fluid m-0">
		<?php 
			if(!$data || !$user){
		?>

			<div class="d-flex align-items-center justify-content-center full-height display-4 header-text"><span class="jumbotron">No Data To Print!</span></div>

		<?php 
				die();
			}
		?>
		<div class="d-flex justify-content-end mb-3 mt-4" id="print-button">
			<button class="btn btn-info font-xl mr-3" onclick="window.print()"><i class="fas fa-print"></i></button>
		</div>
		<div class="d-flex flex-wrap justify-content-around text-muted mb-4 header-text">
			<span><?=$this->time->get_now('d M, Y &#8226 h:iA')?></span>
			<span><?=ucwords("$user->company")?></span>
			<span><?=ucwords($details)?></span>
		</div>
		<div class="header-text text-center">
			<h3><?=ucwords($user->company."'s ".$details)?></h3>
		</div>
		<?php $this->load->view($content)?>
	</div>
</body>
</html>