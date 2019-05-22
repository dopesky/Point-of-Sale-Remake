<form autocomplete="off" method="post" ng-submit="verifyEmailCode($event)">
	<fieldset>
		<div class="form-errors toast p-0 toast-max-width hide fade"><div class="toast-body alert alert-danger mb-0"></div></div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-envelope"></i> </div>
				</div>
				<input type="text" name="code" ng-model="code" class="form-control" placeholder="Input Code Sent to You . . .">
			</div>
			<span class="helper-text" data-original="e.g 12345"></span>
		</div>
		<div class="form-group d-flex flex-wrap justify-content-end">
			<button type="button" ng-show="show_send" ng-click="sendEmailOTP($event)" class="btn btn-info mr-3">Send Code</button>
			<button type="submit" ng-show="show_verify.all && show_verify.verifyEmail" class="btn btn-info width-100">Verify</button>
		</div>
	</fieldset>
</form>