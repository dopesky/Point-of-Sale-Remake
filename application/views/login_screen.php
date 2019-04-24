<form method="post" action="<?=base_url('auth/login')?>" onsubmit="return loginLogic(event)" class="text-center mt-5 fade" autocomplete="off">
	<fieldset>
		<h3 class="header-text">Login <i class="fas fa-sign-in-alt"></i></h3>
		<div class="row mt-3">
			<div class="col-12 col-md-2 col-lg-4"></div>
			<div class="col-12 col-md-8 col-lg-4 p-0">
				<div class="box-shadow ml-3 mr-3 p-3">
					<div id="login-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
					<div class="form-group mb-0">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-at"></i></div>
							</div>
							<input type="email" name="username" class="form-control" placeholder="Email">
						</div>
						<span class="helper-text text-left" data-original='dopesky@example.com'></span>
					</div>
					<div class="text-right"><a href="<?=site_url('auth/forgot_password')?>" tabindex='-1' class="text-info font-sm">Forgot Password?</a></div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
							</div>
							<input type="password" name="password" class="form-control" placeholder="Password">
						</div>
						<span class="helper-text text-left" data-original='********'></span>
					</div>
					<div class="form-group text-right">
						<button type="submit" class="btn btn-info width-100" id="login-button">Login</button>
					</div>
					<div class="timeline-seperator"><span>Don't have an account?</span></div>
					<div class="text-left mt-2 font-sm mb-3"><a href="<?=site_url('auth/create_account')?>" class="text-info">
						<i class="fas fa-plus-square"></i>
						Create an account.
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