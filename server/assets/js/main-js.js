let app = angular.module('main',[]);

app.controller('mainController',['$scope','getApis','register','login','$timeout',function($scope,getApis,register,login,$timeout){
	$scope.userdata = {};
	$scope.apiKeys = [];
	$scope.keysFetched = false;
	$scope.new_scope = 0;
	$scope.apikey_id = '';
	$scope.inputs = {email: '', password: ''};
	$scope.registerUser = async function ($event, $sending, $initial){
		if($scope.inputs.email.length < 1){
			setToast(`<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Fill all Fields!</div>`, 'danger');
			return false;
		}
		let url = $($event.currentTarget).attr('action');
		let button = $($event.currentTarget).find('button');
		toggleButton(button, '<span class="spinner-border spinner-border-sm"></span> ' + $sending);
		let response = await register.save(url, $scope.inputs.email);
		$scope.inputs.email = '';
		$($event.currentTarget).parents('.modal').modal('hide');
		toggleButton(button, $initial);
		if(response.ok){
			setToast(`<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>Check Your Email to Complete Registration!</div>`, 'success');
		}else{
			setPageErrors('#page-errors>div.toast-body', response.errors);
		}
		$scope.$apply();
		return response.ok;
	};
	$scope.loginUser = async function ($event) {
		if($scope.inputs.email.length < 1 || $scope.inputs.password.length < 8){
			setToast("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Fill all Fields!</div>", 'danger');
			return false;
		}
		let url = $($event.currentTarget).attr('action');
		let button = $($event.currentTarget).find('button');
		toggleButton(button, '<span class="spinner-border spinner-border-sm"></span> Signing in . . . ');
		let response = await login.auth(url, $scope.inputs);
		toggleButton(button, 'Login');
		if(response.ok){
			$scope.inputs.email = '';
			$scope.inputs.password = '';
			$scope.userdata = response.userdata;
			$scope.$apply();
			response = await getApis.getApiKeys(apiKeysUrl, $scope.userdata.owner_id);
			$scope.keysFetched = true;
			$scope.apiKeys = response.keys
		}else{
			setPageErrors('#page-errors>div.toast-body', response.errors);
		}
		$scope.$apply();
		return response.ok;
	};
	$scope.generateKey = async function ($event) {
		if($scope.new_scope < 1){
			setToast("<div><i class='fas fa-exclamation-circle'><i><strong> Error: </strong>Please Select an API Key Power First!</div>", 'danger');
			return false;
		}
		let url = $($event.currentTarget).attr('action');
		let button = $($event.currentTarget).find('button');
		toggleButton(button, '<span class="spinner-border spinner-border-sm"></span> Generating . . . ');
		$scope.apiKeys = [];
		$scope.keysFetched = false;
		let response = await getApis.getNewApiKey(url, $scope.userdata.owner_id, $scope.new_scope);
		$($event.currentTarget).parents('.modal').modal('hide');
		if(response.ok){
			$scope.new_scope = 0;
			$scope.keysFetched = true;
			$scope.apiKeys = response.keys;
		}else{
			setPageErrors('#page-errors-logged-in>div.toast-body', response.errors);
		}
		toggleButton(button, '<span class="spinner-border spinner-border-sm"></span> Generate . . . ');
		$scope.$apply();
		return response.ok;
	};
	$scope.copyText = function($index){
		$scope.copy_text = $scope.apiKeys[$index].apikey;
		$timeout(function(){
			$('input[name=copy_text]').select();
			document.execCommand('copy');
			setToast("<div><i class='fas fa-exclamation-circle'><i><strong> Info: </strong>API Key Copied!</div>", 'info');
		});
	};
	$scope.prepareUpdateModal = function($index){
		$scope.scope = parseInt($scope.apiKeys[$index].apikeypower_id);
		$scope.apikey_id = $scope.apiKeys[$index].apikey_id;
	};
	$scope.updateKey = async function($event){
		let url = $($event.currentTarget).parents('form').data('update');
		let updateButton = $($event.currentTarget).parents('form').find('button.btn_info');
		let deleteButton = $($event.currentTarget).parents('form').find('button.btn_danger');
		toggleButton(updateButton, '<span class="spinner-border spinner-border-sm"></span> Updating . . . ');
		deleteButton.attr({'disabled': true}).addClass('disabled');
		$scope.apiKeys = [];
		$scope.keysFetched = false;
		let response = await getApis.updateApiKey(url,$scope.apikey_id,$scope.scope);
		$($event.currentTarget).parents('.modal').modal('hide');
		if(response.ok){
			$scope.keysFetched = true;
			$scope.apiKeys = response.keys;
		}else{
			setPageErrors('#page-errors-logged-in>div.toast-body', response.errors);
		}
		toggleButton(updateButton, 'Update');
		deleteButton.attr({'disabled': false}).removeClass('disabled');
		$scope.$apply();
		return response.ok;
	};
	$scope.deleteKey = async function(deleteButton){
		let url = $(deleteButton).parents('form').data('delete');
		let updateButton = $(deleteButton).parents('form').find('button.btn-info');
		deleteButton = $(deleteButton);
		toggleButton(deleteButton, '<span class="spinner-border spinner-border-sm"></span> Deleting . . . ');
		updateButton.attr({'disabled': true}).addClass('disabled');
		$scope.apiKeys = [];
		$scope.keysFetched = false;
		let response = await getApis.deleteApiKey(url,$scope.apikey_id);
		$(deleteButton).parents('.modal').modal('hide');
		if(response.ok){
			$scope.keysFetched = true;
			$scope.apiKeys = response.keys;
		}else{
			setPageErrors('#page-errors-logged-in>div.toast-body', response.errors);
		}
		toggleButton(deleteButton, 'Delete');
		updateButton.attr({'disabled': false}).removeClass('disabled');
		$scope.$apply();
		return response.ok;
	}
}]);

app.factory('register',[function(){
	return {
		save: function (url, email){
			return $.ajax({
				url,
				data: {email},
				dataType:'json',
				method: 'POST'
			})
		}
	}
}]);

app.factory('login',[function(){
	return {
		auth: function (url, {email, password}){
			return $.ajax({
				url,
				data: {email, password},
				dataType:'json',
				method: 'POST'
			})
		}
	}
}]);

app.factory('getApis',[function(){
	return {
		getApiKeys: function (url, owner_id){
			return $.ajax({
				url,
				data: {owner_id},
				dataType:'json',
				method: 'POST'
			})
		},
		getNewApiKey: function (url, owner_id, scope){
			return $.ajax({
				url,
				data: {owner_id, scope},
				dataType: 'json',
				method: 'POST'
			})
		},
		updateApiKey: function(url, apikey_id, scope){
			return $.ajax({
				url,
				data: {apikey_id, scope},
				dataType: 'json',
				method: 'POST'
			})
		},
		deleteApiKey: function(url, apikey_id){
			return $.ajax({
				url: url,
				data: {apikey_id},
				dataType: 'json',
				method: 'POST'
			})
		}
	}
}]);
