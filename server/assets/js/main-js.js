function viewPassword(span,input){
	$input = $(input).attr('type')
	if($input.localeCompare('password') === 0){
		$(input).attr('type','text')
		$(span).find("i").removeClass('fa-eye').addClass('fa-eye-slash')
	}else{
		$(input).attr('type','password')
		$(span).find("i").removeClass('fa-eye-slash').addClass('fa-eye')
	}
	$(input).focus()
	return false
}

var app = angular.module('main',[])

app.controller('mainController',['$scope','getApis','register','login','$timeout',function($scope,getApis,register,login,$timeout){
	$scope.userdata = {};
	$scope.apiKeys = []
	$scope.keysFetched = false
	$scope.new_scope = 0
	$scope.email = ''
	$scope.password = ''
	$scope.apikey_id = ''
	$scope.registerUser = async function ($event,$sending,$initial){
		if($scope.email.length<1){
			$('#site-info>div.toast>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Fill all Fields!</div>").parent().toast('show')
			return
		}
		var url = $($event.currentTarget).attr('action')
		$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> '+$sending)
		var response = await register.save(url,$scope.email)
		$scope.email = ''
		if(response.ok){
			window.location.reload(true)
		}else{
			$($event.currentTarget).parents('.modal').modal('hide')
			$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html($initial)
			$('#page-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$scope.$apply()
	}
	$scope.loginUser = async function ($event) {
		if($scope.email.length<1 || $scope.password.length<1){
			$('#site-info>div.toast>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Fill all Fields!</div>").parent().toast('show')
			return
		}
		var url = $($event.currentTarget).attr('action')
		$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Signing in . . . ')
		var response = await login.auth(url,$scope.email,$scope.password)
		if(response.ok){
			$scope.email = ''
			$scope.password = ''
			$scope.userdata = response.userdata
			$scope.$apply()
			response = await getApis.getApiKeys(apiKeysUrl,$scope.userdata.owner_id)
			$scope.keysFetched = true
			$scope.apiKeys = response.keys
		}else{
			$('#page-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Login')
		$scope.$apply()
	}
	$scope.generateKey = async function ($event) {
		if($scope.new_scope<1){
			$('#site-info>div.toast>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Select an API Key Power First!</div>").parent().toast('show')
			return
		}
		var url = $($event.currentTarget).attr('action')
		$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Generating . . . ')
		$scope.apiKeys = []
		$scope.keysFetched = false
		var response = await getApis.getNewApiKey(url,$scope.userdata.owner_id,$scope.new_scope)
		$($event.currentTarget).parents('.modal').modal('hide')
		if(response.ok){
			$scope.new_scope = 0
			$scope.keysFetched = true
			$scope.apiKeys = response.keys
		}else{
			$('#page-errors-logged-in>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Generate')
		$scope.$apply()
	}
	$scope.copyText = function($index){
		$scope.copy_text = $scope.apiKeys[$index].apikey
		$timeout(function(){
			$('input[name=copy_text]').select()
			document.execCommand('copy');
			$('#site-info>div.toast>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Info: </strong>API Key Copied!</div>").parent().toast('show')
		})
	}
	$scope.prepareUpdateModal = function($index){
		$scope.scope = parseInt($scope.apiKeys[$index].apikeypower_id)
		$scope.apikey_id = $scope.apiKeys[$index].apikey_id
	}
	$scope.updateKey = async function($event){
		var url = $($event.currentTarget).parents('form').data('update')
		$($event.currentTarget).parents('form').find('button').attr('disabled',true).addClass('disabled').not('.btn-danger').removeClass('width-100').html('<span class="spinner-border spinner-border-sm"></span> Updating . . . ')
		$scope.apiKeys = []
		$scope.keysFetched = false
		var response = await getApis.updateApiKey(url,$scope.apikey_id,$scope.scope)
		$($event.currentTarget).parents('.modal').modal('hide')
		if(response.ok){
			$scope.keysFetched = true
			$scope.apiKeys = response.keys
		}else{
			$('#page-errors-logged-in>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$($event.currentTarget).parents('form').find('button').attr('disabled',false).removeClass('disabled').not('.btn-danger').addClass('width-100').html('Update')
		$scope.$apply()
	}
	$scope.deleteKey = async function(deleteButton){
		var url = $(deleteButton).parents('form').data('delete')
		$(deleteButton).parents('form').find('button').attr('disabled',true).addClass('disabled').not('.btn-info').removeClass('width-100').html('<span class="spinner-border spinner-border-sm"></span> Deleting . . . ')
		$scope.apiKeys = []
		$scope.keysFetched = false
		var response = await getApis.deleteApiKey(url,$scope.apikey_id)
		$(deleteButton).parents('.modal').modal('hide')
		if(response.ok){
			$scope.keysFetched = true
			$scope.apiKeys = response.keys
		}else{
			$('#page-errors-logged-in>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
		}
		$(deleteButton).parents('form').find('button').attr('disabled',false).removeClass('disabled').not('.btn-info').addClass('width-100').html('Delete')
		$scope.$apply()
	}
}])

app.factory('register',[function(){
    return {
	    save: function ($url,$email){
		    return $.ajax({
			    url: $url,
			    data: {email:$email},
			    dataType:'json',
			    method: 'POST'
			})
		}
	}
}])

app.factory('login',[function(){
    return {
	    auth: function ($url,$email,$password){
		    return $.ajax({
			    url: $url,
			    data: {email:$email,password: $password},
			    dataType:'json',
			    method: 'POST'
			})
		}
	}
}])

app.factory('getApis',[function(){
    return {
	    getApiKeys: function ($url,$owner_id){
		    return $.ajax({
			    url: $url,
			    data: {owner_id:$owner_id},
			    dataType:'json',
			    method: 'POST'
			})
		},
		getNewApiKey: function ($url, $owner_id, $scope){
			return $.ajax({
				url: $url,
				data: {
					owner_id: $owner_id,
					scope: $scope
				},
				dataType: 'json',
				method: 'POST'
			})
		},
		updateApiKey: function($url, $apikey_id, $scope){
			return $.ajax({
				url: $url,
				data: {
					apikey_id: $apikey_id,
					scope: $scope
				},
				dataType: 'json',
				method: 'POST'
			})
		},
		deleteApiKey: function($url, $apikey_id){
			return $.ajax({
				url: $url,
				data: {
					apikey_id: $apikey_id
				},
				dataType: 'json',
				method: 'POST'
			})
		}
	}
}])
