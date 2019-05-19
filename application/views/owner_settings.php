<div class="row pl-3 pr-3">
	<main class="col-12 w-100">
		<h2 class="header-text text-center">Owner Settings <i class="fas fa-cog fa-spin"></i></h2>
		<div class="row">
			<div class="col-12 col-md-2"></div>
			<div class="col-12 col-md-8 box-shadow p-0 pt-0">
				<ul class="nav nav-tabs nav-justified tabs rounded-top">
					<li class="nav-item">
					    <a class="nav-link active" data-toggle="tab" href="#profile-setings"><i class="fas fa-user"></i> <span class="d-none d-sm-initial">Profile</span></a>
					</li>
					<li class="nav-item">
					    <a class="nav-link" data-toggle="tab" href="#security-settings"><i class="fas fa-shield-alt"></i> <span class="d-none d-sm-initial">Security</span></a>
					</li>
					<li class="nav-item">
					    <a class="nav-link" data-toggle="tab" href="#other-settings"><i class="fas fa-toolbox"></i> <span class="d-none d-sm-initial">Miscellaneous</span></a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane container active p-3" id="profile-setings">
						<form method="post" class="text-center fade" autocomplete="off">
							<div class="p-3 pt-0">
								<div id="page-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="timeline-seperator"><span>INFO</span></div>
								<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> You Can Change The Details You Used To Register Your Account With! </div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user"></i></div>
										</div>
										<input type="text" name="fname" class="form-control" placeholder="First Name" value="<?=ucwords($user_details->owner_fname)?>">
									</div>
									<span class="helper-text text-left" data-original='e.g John'></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
										</div>
										<input type="text" name="lname" class="form-control" placeholder="Last Name" value="<?=ucwords($user_details->owner_lname)?>">
									</div>
									<span class="helper-text text-left" data-original='e.g Doe'></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-building"></i></div>
										</div>
										<input type="text" name="company" class="form-control" placeholder="Company Name" value="<?=ucwords($user_details->company)?>">
									</div>
									<span class="helper-text text-left" data-original='e.g POS'></span>
								</div>
								<div class="form-group text-right mb-2">
									<button type="submit" class="btn btn-info width-100" id="change-button">Change</button>
								</div>
							</div>
						</form>	
					</div>
					<div class="tab-pane container fade" id="security-settings">
						
					</div>
					<div class="tab-pane container fade" id="other-settings">...</div>
				</div>
			</div>
			<div class="col-12 col-md-2"></div>
		</div>
	</main>
</div>

<script>
	var app = angular.module('main')
	//app.controller('')
</script>