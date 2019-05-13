<form method="post" action="<?=base_url("auth/password_reset/$token/$id")?>" onsubmit="return changePasswordLogic(event)" class="text-center mt-5 fade" autocomplete="off">
	<fieldset>
		<h3 class="header-text">Change Password <i class="fas fa-key"></i></h3>
		<div class="row mt-3">
			<div class="col-12 col-md-2 col-lg-4"></div>
			<div class="col-12 col-md-8 col-lg-4 p-0">
				<div class="box-shadow ml-3 mr-3 p-3 pt-1">
					<div id="page-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
					<div class="timeline-seperator"><span>INFO</span></div>
					<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Passwords must contain atleast one uppercase, one lowercase and one numeric character and must be atleast 8 characters long.<br> For example, <u><i>Example1234<i></u> is a valid password.</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
							</div>
							<input type="password" name="new_password" class="form-control" placeholder="New Password">
							<div class="input-group-append">
								<div class="input-group-text"><a href="#" tabindex="-1" class="text-info" onclick="return viewPassword(this,'input[name=new_password]')"><i class="fas fa-eye"></i></a></div>
							</div>
						</div>
						<span class="helper-text text-left" data-original='***********'></span>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
							</div>
							<input type="password" name="repeat_password" class="form-control" placeholder="Repeat Password">
							<div class="input-group-append">
								<div class="input-group-text"><a href="#" tabindex="-1" class="text-info" onclick="return viewPassword(this,'input[name=repeat_password]')"><i class="fas fa-eye"></i></a></div>
							</div>
						</div>
						<span class="helper-text text-left" data-original='***********'></span>
					</div>
					<div class="form-group text-right mb-2">
						<button type="submit" class="btn btn-info width-100" id="change-password-button">Change</button>
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