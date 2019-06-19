<?php 
$authenticator = new PHPGangsta_GoogleAuthenticator();
$qrCodeUrl = ($user_details->twofactor_secret) ? $authenticator->getQRCodeGoogleUrl('Point of Sale ('.$user_details->email.")", $user_details->twofactor_secret) : null;
$photo = null;
if($user_details->owner_photo && $user_details->id_owner){
	$photo = $user_details->owner_photo;
}elseif($user_details->profile_photo && $user_details->owner_id){
	$photo = $user_details->profile_photo;
}else{
	$photo = 'https://res.cloudinary.com/dopesky/image/upload/v1558329489/point_of_sale/site_data/blank-profile-picture-973460_960_720_gcn9y2.png';
}
?>

<div class="row pl-3 pr-3" ng-app="main" ng-cloak ng-controller="settingsController" ng-init="user.fname = '<?=($user_details->owner_fname) ? ucwords($user_details->owner_fname) : ucwords($user_details->first_name)?>';user.lname = '<?= ($user_details->owner_lname) ? ucwords($user_details->owner_lname) : ucwords($user_details->last_name)?>';user.company = '<?=ucwords($user_details->company)?>';user.photo = '<?=$photo?>';user.twoFA = <?=$user_details->twofactor_auth?>;user.token = '<?=$user_details->twofactor_secret?>';user.url = '<?=$qrCodeUrl?>';user.showInactive = <?=$user_details->show_inactive?>;user.showDeleted = <?=$user_details->show_deleted?>;user.country = '<?=strtolower($user_details->country_name)?>'">
	<main class="col-12 w-100">
		<h2 class="header-text text-center">Owner Settings <i class="fas fa-cog fa-spin"></i></h2>
		<div class="row">
			<div class="col-12 col-md-2"></div>
			<div class="col-12 col-md-8 box-shadow p-0 pt-0 mb-3">
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
				<div class="tab-content limit-tab-content-lower">
					<div class="tab-pane container active p-3" id="profile-setings">
						<?php
							if((int)$this->session->userdata('userdata')['level'] < 4)
								$this->load->view('employee_profile_settings');
							else
								$this->load->view('owner_profile_settings');
						?>	
					</div>
					<div class="tab-pane container fade" id="security-settings">
						<?=$this->load->view('security_settings',array(),true)?>	
					</div>
					<div class="tab-pane container fade" id="other-settings">
						<?=$this->load->view('miscelleneous_settings',array(),true)?>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-2"></div>
		</div>
	</main>
	<div class="modal fade" data-backdrop="static" id="password-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted header-text"><i class="fas fa-lock"></i> Locked</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="timeline-seperator mb-3"><span>INFO</span></div>
					<div class="text-muted mb-3 font-sm text-left"><i class="fas fa-info-circle"></i> To Perform This Action You need To Enter Your Password! </div>
					<form autocomplete="off" ng-submit="changeEmailOrPassword($event)">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
								</div>
								<input type="password" name="password" ng-model="user.password" class="form-control" placeholder="Password">
								<div class="input-group-append">
									<div class="input-group-text"><a href="#" tabindex="-1" class="text-info" onclick="return viewPassword(this,'input[name=password]')"><i class="fas fa-eye"></i></a></div>
								</div>
							</div>
							<span class="helper-text text-left" data-original='********'></span>
						</div>
						<div class="form-group text-right mb-2">
							<button type="submit" class="btn btn-info width-100">Go!</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-muted text-left" id="show-QR-code" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3>Google Authenticator QR Code</h3>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="timeline-seperator mb-3"><span>INFO</span></div>
					<div class="font-sm mb-3"><i class="fas fa-info-circle"></i> Download and Open Google Authenticator from your Mobile App Store then Scan this Image or Input the Code Below it to be Able to Use Google Authenticator for 2 Step Authentication on Login.</div>
					<div class="row">
						<div class="col-12">
							<div class="d-flex flex-wrap justify-content-center mb-3">
								<img ng-src="{{user.url}}">
							</div>
							<div class="d-flex flex-wrap justify-content-center">
								<span><b>QR Code:</b> {{user.token}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var app = angular.module('main')
	app.controller('settingsController',['$scope','saveSettings',function($scope,saveSettings){
		$scope.label = "Update Company Logo . . ."
		$scope.profile = "Update Profile Photo . . ."
		$scope.actionEmail = null
		$scope.saveCountry = false
		$scope.$watch("user.twoFA", function (value) {
		  	$scope.user.twoFA = Boolean(value);
		})
		$scope.$watch("user.showInactive", function (value) {
		  	$scope.user.showInactive = Boolean(value);
		})
		$scope.$watch("user.showDeleted", function (value) {
		  	$scope.user.showDeleted = Boolean(value);
		})
		$scope.change = function($event){
			var fileName = $($event.currentTarget).val().split("\\").pop();
			if(!fileName){
				$scope.label = "Update Company Logo . . .";
				$scope.profile = "Update Profile Photo . . .";
			}else{
				$scope.label = fileName;
				$scope.profile = fileName;
			}
			$scope.$apply();
		}
		$scope.updateDetails = async function($event){
			$event.preventDefault()
			$scope.user.file = ($('#customFile').length > 0) ? (($('#customFile')[0].files.length > 0) ? $('#customFile')[0].files[0] : null) : null
			$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Changing . . .')
			var response = await saveSettings.saveOwnerDetails($scope.user)
			$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Change')
			$('#customFile').val("")
			$scope.label = "Update Company Logo . . .";
			if(response.ok){
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				$scope.user.photo = response.photo
			}else{
				if(response.code === 409){
					$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
				}else{
					$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
				}
			}
			$('body,html').animate({scrollTop:0})
			$scope.$apply()
		}
		$scope.updateDetailsEmployee = async function($event){
			$event.preventDefault()
			$scope.user.file = ($('#customFile').length > 0) ? (($('#customFile')[0].files.length > 0) ? $('#customFile')[0].files[0] : null) : null
			$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Changing . . .')
			var response = await saveSettings.saveEmployeeDetails($scope.user)
			$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Change')
			$('#customFile').val("")
			$scope.profile = "Update Profile Photo . . .";
			if(response.ok){
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				$scope.user.photo = response.photo
			}else{
				if(response.code === 409){
					$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
				}else{
					$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
				}
			}
			$('body,html').animate({scrollTop:0})
			$scope.$apply()
		}
		$scope.displayChangeEmailModal = function($event){
			$event.preventDefault()
			reset_helper_texts()
			if(!$scope.user.email || $scope.user.email.length < 1 || !test_email($scope.user.email)){
				change_helper_texts($('input[name=email]').parent().siblings('.helper-text'),'Email is of Invalid Format!','#dc3545')
				return;
			}
			$scope.actionEmail = true
			$('#password-modal').modal('show')
		}
		$scope.displayChangePasswordModal = function($event){
			$event.preventDefault()
			reset_helper_texts()
			if(!$scope.user.newPass || $scope.user.newPass.length < 8){
				change_helper_texts($('input[name=new_password]').parent().siblings('.helper-text'),'Password is Required and Must be at Least 8 Characters Long!','#dc3545')
				return;
			}
			if(!$scope.user.newPass || $scope.user.newPass.localeCompare($scope.user.repeatPass) !== 0){
				change_helper_texts($('input[name=repeat_password]').parent().siblings('.helper-text'),'Password is Required and Must Match!','#dc3545')
				return;
			}
			$scope.actionEmail = false
			$('#password-modal').modal('show')
		}
		$scope.changeEmailOrPassword = async function($event){
			$('#password-modal').modal('hide')
			$event.preventDefault()
			reset_helper_texts()
			switch($scope.actionEmail){
				case true:
					if(!$scope.user.password || $scope.user.password.length < 8){
						$('input[name=email]').parents('form').find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>Wrong/Invalid Password</div>").parent().toast({delay:6500}).toast('show')
						return;
					}
					if(!$scope.user.email || $scope.user.email.length < 1 || !test_email($scope.user.email)){
						change_helper_texts($('input[name=email]').parent().siblings('.helper-text'),'Email is of Invalid Format!','#dc3545')
						return;
					}
					$('input[name=email]').parents('form').find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Changing . . .')
					var response = await saveSettings.changeEmail($scope.user);
					$('input[name=email]').parents('form').find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Change')
					$scope.actionEmail = null
					if(response.ok){
						$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
						$('body,html').animate({scrollTop:0})
						$scope.user.email = ""
					}else{
						$('input[name=email]').parents('form').find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:6500}).toast('show')
					}
					$scope.user.password = ""
					$scope.$apply()
					break;
				case false:
					if(!$scope.user.newPass || $scope.user.newPass.length < 8){
						change_helper_texts($('input[name=new_password]').parent().siblings('.helper-text'),'Password is Required and Must be at Least 8 Characters Long!','#dc3545')
						return;
					}
					if(!$scope.user.newPass || $scope.user.newPass.localeCompare($scope.user.repeatPass) !== 0){
						change_helper_texts($('input[name=repeat_password]').parent().siblings('.helper-text'),'Password is Required and Must Match!','#dc3545')
						return;
					}
					$('input[name=new_password]').parents('form').find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Changing . . .')
					var response = await saveSettings.changePassword($scope.user);
					$('input[name=new_password]').parents('form').find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Change')
					$scope.actionEmail = null
					if(response.ok){
						$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
						$('body,html').animate({scrollTop:0})
						$scope.user.newPass = ""
						$scope.user.repeatPass = ""
					}else{
						$('input[name=new_password]').parents('form').find('.form-errors>div.toast-body').removeClass('alert-danger alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:6500}).toast('show')
					}
					$scope.user.password = ""
					$scope.$apply()
					break;
			}
		}
		$scope.onChange2FA = async function(){
			if($scope.user.twoFA){
				var response = await saveSettings.changeTwoFA('activate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong><br><br>2 Step Authentication has been Activated</div>").parent().toast('show')
					if(!$scope.user.token) $('#show-QR-code').modal('show')
					$scope.user.token = response.response.secret
					$scope.user.url = response.response.url
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}else{
				var response = await saveSettings.changeTwoFA('deactivate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}
			$('body,html').animate({scrollTop:0})
		}
		$scope.onChangeShowInactive = async function(){
			if($scope.user.showInactive){
				var response = await saveSettings.changeShowInactive('activate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}else{
				var response = await saveSettings.changeShowInactive('deactivate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}
			$('body,html').animate({scrollTop:0})
		}
		$scope.onChangeShowDeleted = async function(){
			if($scope.user.showDeleted){
				var response = await saveSettings.changeShowDeleted('activate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}else{
				var response = await saveSettings.changeShowDeleted('deactivate');
				if(response.ok){
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				}else{
					$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
				}
				$scope.$apply()
			}
			$('body,html').animate({scrollTop:0})
		}
		$scope.onChangeCountry = async function(){
			var response = await saveSettings.changeCountry($scope.user);
			if(response.ok){
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
			}else{
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>"+response.errors+"</div>").parent().toast('show')
			}
			$('body,html').animate({scrollTop:0})
		}
	}])
	app.factory('saveSettings',function(){
		return {
			saveOwnerDetails: function({fname,lname,company,file}){
				serializedData = new FormData()
            	serializedData.append('fname',fname)
            	serializedData.append('lname',lname)
            	serializedData.append('company',company)
            	serializedData.append('file',file)
				return $.ajax({
					url: `${base_url}settings/update_owner_details`,
					data: serializedData,
					processData: false,
					contentType: false,
					dataType: 'json',
					method: 'POST'
				})
			},
			saveEmployeeDetails: function({fname,lname,file}){
				serializedData = new FormData()
            	serializedData.append('fname',fname)
            	serializedData.append('lname',lname)
            	serializedData.append('file',file)
				return $.ajax({
					url: `${base_url}settings/update_employee_details`,
					data: serializedData,
					processData: false,
					contentType: false,
					dataType: 'json',
					method: 'POST'
				})
			},
			changeEmail: function({email, password}){
				return $.ajax({
					url: `${base_url}settings/change_email`,
					data: {email, password},
					dataType: 'json',
					method: 'POST'
				})
			},
			changePassword: function({password, newPass, repeatPass}){
				return $.ajax({
					url: `${base_url}settings/change_password`,
					data: {password, newPass, repeatPass},
					dataType: 'json',
					method: 'POST'
				})
			},
			changeTwoFA: function(action){
				return $.ajax({
					url: `${base_url}settings/enable_disable_2FA`,
					data: {action},
					dataType: 'json',
					method: 'POST'
				})
			},
			changeShowInactive: function(action){
				return $.ajax({
					url: `${base_url}settings/enable_disable_show_inactive`,
					data: {action},
					dataType: 'json',
					method: 'POST'
				})
			},
			changeShowDeleted: function(action){
				return $.ajax({
					url: `${base_url}settings/enable_disable_show_deleted`,
					data: {action},
					dataType: 'json',
					method: 'POST'
				})
			},
			changeCountry: function({country}){
				return $.ajax({
					url: `${base_url}settings/change_country`,
					data: {country},
					dataType: 'json',
					method: 'POST'
				})
			}
		}
	})
</script>