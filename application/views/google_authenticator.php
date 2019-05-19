<form autocomplete="off" method="post" ng-submit="verifyGoogleCode($event)">
	<fieldset>
		<div class="form-errors toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-mobile-alt"></i> </div>
				</div>
				<input type="text" name="code" ng-model="code" class="form-control" placeholder="Open Google Authenticator and Input Code . . .">
			</div>
			<span class="helper-text" data-original="e.g 12345"></span>
		</div>
		<div class="form-group">
			<button type="submit" ng-show="show_verify.all && show_verify.verifyGoogle" class="btn btn-info float-right width-100">Verify</button>
		</div>
	</fieldset>
</form>