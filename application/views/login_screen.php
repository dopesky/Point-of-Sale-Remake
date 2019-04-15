<form method="post" action="<?=base_url('auth/login')?>" onsubmit="return loginLogic(event)" class="text-center mt-5" autocomplete="off">
	<fieldset>
		<div class="display-4 header-text">Login <i class="fas fa-sign-in-alt"></i></div>
		<div class="row mt-3">
			<div class="col-sm-12 col-md-2 col-lg-4"></div>
			<div class="col-sm-12 col-md-8 col-lg-4 p-0">
				<div class="box-shadow ml-3 mr-3 p-3">
					<div id="login-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-user"></i></div>
							</div>
							<input type="text" name="username" class="form-control" placeholder="Username">
						</div>
						<span class="helper-text text-left" data-original='@dopesky'></span>
					</div>
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
						<button type="submit" class="btn btn-primary width-100">Login</button>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-2 col-lg-4"></div>
		</div>
	</fieldset>
</form>
<script>
	$($('form')[0]).hide()
	$('body').addClass('with-background')
</script>