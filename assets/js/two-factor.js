var app = angular.module('main')
app.controller('twoFactorAuth',['$scope','OTP',function($scope,OTP){
	$scope.code = '';
	$scope.qrCodeUrl = ''
	$scope.qrCode = ''
	$scope.show_send = true;
	$scope.show_verify = {
		verifyGoogle: true,
		verifyEmail: true,
		all: true
	}
	$scope.sendEmailOTP = async function($event){
		$scope.show_verify.all = false
		$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Sending . . .')
		var response = await OTP.sendEmailOTP()
		$scope.show_verify.all = true
		if(response.ok){
			$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('Re-send Code')
			$scope.code = ''
			$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
		}else{
			$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('Send Code')
			if(response.code === 503)
				$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').removeClass('alert-danger').addClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Warning: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			else
				$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').removeClass('alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$scope.$apply()
	}
	$scope.verifyEmailCode = async function($event){
		$event.preventDefault()
		reset_helper_texts()
		if(!$scope.code || $scope.code.length < 1){
			change_helper_texts($($event.currentTarget).find('input[name=code]').parent().siblings('.helper-text'),'This is Required!','#dc3545')
			return
		}
		if(/[^0-9]/i.test($scope.code.trim())){
			change_helper_texts($($event.currentTarget).find('input[name=code]').parent().siblings('.helper-text'),'Code Should Consist of Only Numbers!','#dc3545')
			return
		}
		$scope.show_verify.verifyGoogle = false
		$scope.show_send = false
		$($event.currentTarget).find('button[type=submit]').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Verifying . . .')
		var response = await OTP.verifyEmailCode($scope.code)
		$($event.currentTarget).find('button[type=submit]').attr('disabled',false).addClass('width-100').removeClass('disabled').html('Verify')
		$scope.show_verify.verifyGoogle = true
		$scope.show_send = true
		if(response.ok){
			window.location.reload(true)
		}else{
			$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$scope.$apply()
	}
	$scope.verifyGoogleCode = async function($event){
		$event.preventDefault()
		reset_helper_texts()
		if(!$scope.code || $scope.code.length < 1){
			change_helper_texts($($event.currentTarget).find('input[name=code]').parent().siblings('.helper-text'),'This is Required!','#dc3545')
			return
		}
		if(/[^0-9]/i.test($scope.code.trim())){
			change_helper_texts($($event.currentTarget).find('input[name=code]').parent().siblings('.helper-text'),'Code Should Consist of Only Numbers!','#dc3545')
			return
		}
		$scope.show_verify.verifyEmail = false
		$scope.show_send = false
		$($event.currentTarget).find('button[type=submit]').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Verifying . . .')
		var response = await OTP.verifyGoogleCode($scope.code)
		$($event.currentTarget).find('button[type=submit]').attr('disabled',false).addClass('width-100').removeClass('disabled').html('Verify')
		$scope.show_verify.verifyEmail = true
		$scope.show_send = true
		if(response.ok){
			window.location.reload(true)
		}else{
			if(response.code === 409){
				$scope.qrCodeUrl = response.errors.url
				$scope.qrCode = response.errors.secret
				$scope.code = ''
				$('#show-QR-code').modal('show')
			}else{
				$($event.currentTarget).find('.form-errors>div.toast-body').removeClass('alert-warning').addClass('alert-danger').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			}
		}
		$scope.$apply()
	}

}])
app.factory('OTP',[function(){
	return {
		sendEmailOTP: function(){
			return $.ajax({
				url: `${base_url}auth/send_email_otp`,
				dataType: 'json',
				method: 'post'
			})
		},
		verifyEmailCode: function(code){
			return $.ajax({
				url: `${base_url}auth/verify_otp_token`,
				data: {code},
				dataType: 'json',
				method: 'post'
			})
		},
		verifyGoogleCode: function(code){
			return $.ajax({
				url: `${base_url}auth/verify_google_auth_token`,
				data: {code},
				dataType: 'json',
				method: 'post'
			})
		}
	}
}])