<form method="post" action="<?=site_url('owner/register_owner')?>" onsubmit="return registerOwnerLogic(event)" class="text-center mt-5 fade" autocomplete="off">
	<fieldset>
		<h3 class="header-text">Complete Registration <i class="fas fa-check-square"></i></h3>
		<div class="row mt-3">
			<div class="col-12 col-md-2 col-lg-4"></div>
			<div class="col-12 col-md-8 col-lg-4 p-0">
				<div class="box-shadow ml-3 mr-3 p-3 pt-1">
					<div id="page-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
					<div class="timeline-seperator"><span>INFO</span></div>
					<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Finish Up Creating Your Account by Providing The Following Details! </div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-user"></i></div>
							</div>
							<input type="text" name="fname" class="form-control" placeholder="First Name">
						</div>
						<span class="helper-text text-left" data-original='e.g John'></span>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
							</div>
							<input type="text" name="lname" class="form-control" placeholder="Last Name">
						</div>
						<span class="helper-text text-left" data-original='e.g Doe'></span>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-building"></i></div>
							</div>
							<input type="text" name="company" class="form-control" placeholder="Company Name">
						</div>
						<span class="helper-text text-left" data-original='e.g POS'></span>
					</div>
					<div class="form-group text-right mb-2">
						<button type="submit" class="btn btn-info width-100" id="finish-button">Finish</button>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-2 col-lg-4"></div>
		</div>
	</fieldset>
</form>
<script>
	$('body').addClass('with-background')
</script>