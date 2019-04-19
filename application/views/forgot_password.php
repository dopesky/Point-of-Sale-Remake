<form method="post" action="<?=base_url('auth/send_reset_email')?>" class="text-center mt-5 fade" autocomplete="off" onsubmit="return signUpLogic(event,'#forgot-password-buttton','Reset','Resetting . . .')">
	<fieldset>
		<h3 class="header-text">Forgot Password <i class="fas fa-question-circle"></i></h3>
		<div class="row mt-3">
			<div class="col-12 col-md-2 col-lg-4"></div>
			<div class="col-12 col-md-8 col-lg-4 p-0">
				<div class="box-shadow ml-3 mr-3 p-3 pt-1">
					<div id="page-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
					<div class="timeline-seperator"><span>INFO</span></div>
					<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Input your email and a password reset link will be sent to you! </div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-at"></i></div>
							</div>
							<input type="email" name="username" class="form-control" placeholder="Email">
						</div>
						<span class="helper-text text-left" data-original='dopesky@example.com'></span>
					</div>
					<div class="form-group text-right mb-2">
						<button type="submit" class="btn btn-info width-100" id="forgot-password-buttton">Reset</button>
					</div>
					<div class="timeline-seperator"><span>OR</span></div>
					<div class="text-left mt-2 font-sm mb-3"><a href="<?=base_url()?>" class="text-info">
						<i class="fas fa-sign-in-alt"></i>
						I Remember My Password!
					</a></div>
				</div>
			</div>
			<div class="col-12 col-md-2 col-lg-4"></div>
		</div>
	</fieldset>
</form>
<script>
	$('body').addClass('with-background')
</script>