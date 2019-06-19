<form method="post" class="text-center fade" autocomplete="off" enctype="multipart/form-data" ng-submit="updateDetailsEmployee($event)">
	<div class="p-3 pt-0 pb-0">
		<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
		<div class="timeline-seperator"><span>INFO</span></div>
		<div class="text-muted mt-2 mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> You Can Change The Details You Used To Register Your Account With! </div>
		<div class="row">
			<div class="col-12 mb-3">
				<img ng-src="{{user.photo}}" class="img-header img-thumbnail">
			</div>
		</div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-user"></i></div>
				</div>
				<input type="text" name="fname" class="form-control" placeholder="First Name" ng-model="user.fname">
			</div>
			<span class="helper-text text-left" data-original='e.g John'></span>
		</div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
				</div>
				<input type="text" name="lname" class="form-control" placeholder="Last Name" ng-model="user.lname">
			</div>
			<span class="helper-text text-left" data-original='e.g Doe'></span>
		</div>
		<div class="form-group text-left">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-image"></i></div>
				</div>
				<div class="custom-file">
					<input type="file" onchange="angular.element(this).scope().change(event)" accept="image/*" name="profile_photo" class="custom-file-input" id="customFile">
					<label class="custom-file-label" for="customFile">{{profile}}</label>
				</div>
			</div>
			<span class="helper-text" data-original="Your Profile Photo Uniquely Identifies You."></span>
		</div>
		<div class="form-group text-right mb-2">
			<button type="submit" class="btn btn-info width-100">Change</button>
		</div>
	</div>
</form>