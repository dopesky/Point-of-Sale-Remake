<div class="row" ng-app="main" ng-controller="mainController" ng-cloak>
	<div class="col-md-2 col-lg-4"><input type="text" class="fade" ng-model="copy_text" name="copy_text"></div>
	<div class="col-md-8 col-lg-4 mt-5">
		<h3 class="header-text text-center">POS API Keys <i class="fas fa-key"></i></h3>
		<form ng-show="!userdata.owner_id" class="box-shadow p-3" autocomplete="off" method="POST" action="<?=site_url('api_key/login')?>" ng-submit="loginUser($event)">
			<div id="page-errors" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-prepend">
						<div class="input-group-text">@</div>
					</div>
					<input type="text" name="email" ng-model="email" class="form-control" placeholder="Email . . .">
				</div>
			</div>
			<div class="text-right"><a href="#password-reset-modal" data-toggle="modal" tabindex='-1' class="text-info font-sm">Forgot Password?</a></div>
			<div class="form-group">
				<div class="input-group">
					<input type="password" name="password" ng-model="password" class="form-control" placeholder="Password . . .">
					<div class="input-group-append">
						<div class="input-group-text"><a href="#" tabindex="-1" class="text-info" onclick="return viewPassword(this,'input[name=password]')"><i class="fas fa-eye"></i></a></div>
					</div>
				</div>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-info width-100">Login</button>
			</div>
			<div class="timeline-seperator mb-2"><span>Don't have an Account?</span></div>
			<a href="#sign-up-modal" data-toggle="modal" class="text-info font-sm"><i class="fas fa-plus-square"></i> Create an Account.</a>
		</form>
		<div ng-show="userdata.owner_id" class="box-shadow p-3 pt-1">
			<div class="w-100 text-right mb-1"><a href="" onclick="window.location.reload()" class="text-danger mb-1 header-text"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
			<div id="page-errors-logged-in" class="toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
			<h4 class="text-center text-info mb-3" ng-show="!keysFetched"><i class="spinner-border"></i> Fetching Your Api Keys . . . </h4>
			<div class="text-center text-muted mb-3" ng-show="keysFetched && !apiKeys.length">No API Keys Created!</div>
			<div ng-show="keysFetched && apiKeys.length" class="table-responsive-sm">
				<table class="table table-hover table-bordered table-striped">
					<thead class="thead-light">
						<tr>
							<th>API Key</th>
							<th>API Key Scope</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="apiKey in apiKeys">
							<td align="center"><a href="" onclick="return false" ng-click="copyText($index)" title="{{apiKey.apikey}}" class="text-info"><i class="fas fa-copy"></i> Copy</a></td>
							<td align="center" ng-class="apiKey.apikey_power == 'BOTH' ? 'text-success' : 'text-danger'">{{apiKey.apikey_power}}</td>
							<td align="center"><a href="#update-api-key-modal" data-toggle="modal" ng-click="prepareUpdateModal($index)" class="text-info"><i class="fas fa-cog fa-spin"></i></a></td>
						</tr>
					</tbody>
				</table>
				<span class="text-dark">{{apiKey.owner_email}}</span>
			</div>
			<div ng-show="keysFetched" class="row">
				<div class="col-12">
					<div class="timeline-seperator mb-3"><span>READ ME</span></div>
					<div class="text-muted mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Be Careful on Scopes! <b>READ</b> Scope Means the Key can only Perform Read Operations and Write-Scoped Keys can only Perform <b>WRITE</b> Functions (insert, update & delete). <b>BOTH</b> Scope Means it Performs Both Operations!</div>
					<button data-toggle="modal" data-target="#new-api-key-modal" class="float-right btn btn-success font-sm"><i class="fas fa-plus-square"></i> New Api Key.</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2 col-lg-4">
		<div class="modal fade" id="sign-up-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="text-muted">Create Account <i class="fas fa-plus-square"></i></h4>
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<form method="post" action="<?=site_url('api_key/register')?>" autocomplete="off" ng-submit="registerUser($event,'Creating . . .','Create')">
							<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Enter Your Email to Register to this Site and get API Keys!<br> Use the Keys Generated for You to Gain Access to our API!</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">@</div>
									</div>
									<input type="text" name="email" ng-model="email" class="form-control" placeholder="Email . . .">
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-info width-100 float-right" id="sign-up-button">Create</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="password-reset-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="text-muted">Forgot Password <i class="fas fa-question-circle"></i></h4>
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<form method="post" action="<?=site_url('api_key/send_reset_email')?>" autocomplete="off" ng-submit="registerUser($event,'Resetting . . . ','Reset')">
							<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Enter Your Email to Reset your API Account Password!</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">@</div>
									</div>
									<input type="text" name="email" ng-model="email" class="form-control" placeholder="Email . . .">
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-info width-100 float-right" id="password-reset-button">Reset</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="new-api-key-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="text-muted">New API Key <i class="fas fa-plus-square"></i></h4>
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<form method="post" action="<?=site_url('api_key/generate_api_key')?>" autocomplete="off" ng-submit="generateKey($event)">
							<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> Enter Your Key Scope to Receive a New API Key</div>
							<div class="form-group">
								<select name="scope" ng-model="new_scope" class="custom-select">
									<option ng-value="0" disabled>Select the Power of your API Key</option>
									<?php foreach($powers as $power){?>
								    <option ng-value="<?=$power->apikeypower_id?>"><?=ucfirst(strtolower($power->apikey_power))?></option>
								    <?php }?>
								</select>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-info width-100 float-right" id="new-api-key-button">Generate</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="update-api-key-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="text-muted">Update/Delete API Key <i class="fas fa-cog fa-spin"></i></h4>
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<form method="post" data-update="<?=site_url('api_key/update_api_key')?>" data-delete="<?=site_url('api_key/delete_api_key')?>" autocomplete="off">
							<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> You can only Change the Scope of your API Key! You can Also Delete This API Key!</div>
							<div class="form-group">
								<select name="scope" ng-model="scope" class="custom-select">
									<option ng-value="0" disabled>Select the Power of your API Key</option>
									<?php foreach($powers as $power){?>
								    <option ng-value="<?=$power->apikeypower_id?>"><?=ucfirst(strtolower($power->apikey_power))?></option>
								    <?php }?>
								</select>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-danger width-100 float-right mb-3" data-target="#confirm-deletion-modal" data-toggle="modal" id="delete-api-key-button">Delete</button>
								<button type="submit" class="btn btn-info width-100 float-right mr-3" ng-click="updateKey($event)" id="update-api-key-button">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" data-backdrop="static" id="confirm-deletion-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="text-danger">Are You Sure <i class="fas fa-question-circle"></i></h4>
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body text-muted pt-4 pb-4">
						<i class="fas fa-exclamation-triangle"></i> Are you Sure you Want to Delete this API Key?<br>This action <b>CANNOT</b> be Undone!
					</div>
					<div class="modal-footer pt-4 pb-4">
						<button type="button" class="btn btn-danger width-100" data-dismiss="modal" ng-click="deleteKey('#delete-api-key-button')">YES</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('form').submit(event=>{
		event.preventDefault()
	})
</script>