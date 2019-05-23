<div class="mt-3 p-3 pt-0 pb-0">
	<div>
		<ul class="list-group text-secondary">
			<li class="list-group-item bg-translucent d-flex justify-content-between align-items-center">
				<span>Enable 2 Step Authentication <small class="text-muted"><i>(Recommended)</i></small><br><small ng-show="user.token.length"><a href="#show-QR-code" data-toggle='modal'>View QR Image</a></small></span>
				<span class="custom-control custom-switch">
				    <input type="checkbox" ng-change="onChange2FA()" class="custom-control-input" ng-model="user.twoFA" id="2fa">
				    <label class="custom-control-label" for="2fa"></label>
				</span>
			</li>
		</ul>
	</div><hr>
	<form autocomplete="off" ng-submit="displayChangeEmailModal($event)">
		<legend class="text-muted header-text"><u>Change Email</u></legend>
		<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Here You Can Change Your Official Email! </div>
		<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-at"></i></div>
				</div>
				<input type="email" name="email" ng-model="user.email" placeholder="Enter New Email . . ." class="form-control">
				<div class="input-group-append">
					<button type="submit" class="btn btn-info width-100">Change</button>
				</div>
			</div>
			<span class="helper-text" data-original="e.g dopesky@example.com"></span>
		</div>
	</form><hr>
	<form autocomplete="off" ng-submit="displayChangePasswordModal($event)">
		<legend class="text-muted header-text"><u>Change Password</u></legend>
		<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Here You Can Change Your Password! </div>
		<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
				</div>
				<input type="password" name="new_password" ng-model="user.newPass" class="form-control" placeholder="New Password . . .">
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
				<input type="password" name="repeat_password" ng-model="user.repeatPass" class="form-control" placeholder="Repeat Password . . .">
				<div class="input-group-append">
					<div class="input-group-text"><a href="#" tabindex="-1" class="text-info" onclick="return viewPassword(this,'input[name=repeat_password]')"><i class="fas fa-eye"></i></a></div>
				</div>
			</div>
			<span class="helper-text text-left" data-original='***********'></span>
		</div>
		<div class="form-group text-right mb-2">
			<button type="submit" class="btn btn-info width-100">Change</button>
		</div>
	</form><hr>
	<div class="border border-danger clearfix p-3 text-danger rounded bg-translucent">
		<legend class="header-text"><u>Delete Account</u></legend>
		<div class="mt-2 mb-3 font-sm text-left"><i class="fas fa-exclamation-triangle"></i> This is a dangerous Action You are about to perform! Be sure you understand the consequences before undertaking it. All your employees will not be able to Log in to their accounts. </div>
		<button class="btn btn-outline-danger float-right">Delete Account</button>
	</div>
</div>