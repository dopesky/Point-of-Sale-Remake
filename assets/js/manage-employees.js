var app = angular.module('main')
app.controller('manageEmployees',['$scope','DTOptionsBuilder','DTDefaultOptions','DTColumnBuilder','saveEmployeeDetails', '$timeout', function($scope,DTOptionsBuilder,DTDefaultOptions,DTColumnBuilder,saveEmployeeDetails,$timeout){
	$scope.inputFields = {
		fname: '',
		lname: '',
		email: '',
		department: '0'
	}
	$scope.updateFields = {
		id: '',
		fname: '',
		lname: '',
		email: '',
		department: '0'
	}
	$scope.errorSpan = {
		fname: 'input[name=fname]',
		lname: 'input[name=lname]',
		email: 'input[name=email]',
		department: 'select[name=department]'
	}
	$scope.showButton = {
		all: true,
		update: true,
		reemploy: false,
		unemploy: false
	}
	$scope.showFooter = false
	$scope.tableInstance = {};
	$scope.renderTable = function(){
		DTDefaultOptions.setDisplayLength(25).setDOM('<"row"<"col-sm-6 add-employee-button"><"col-sm-6 d-flex justify-content-sm-start justify-content-end"f>>rtp').setLanguage({
            "paginate": {
                "next": '<i class="fas fa-forward" aria-hidden="true"></i>',
                "previous": '<i class="fas fa-backward" aria-hidden="true"></i>'
            },
            "search": "<div class='input-group'>_INPUT_<div class='input-group-append'><div class='input-group-text'><i class='fas fa-search'></i></div></div></div>",
            "searchPlaceholder": "Search for Employee . . ."
        }).setOption('responsive',true)
		$scope.tableOptions = DTOptionsBuilder.fromSource(`${base_url}owner/get_employees/${user_id}`)
		$scope.tableColumns = [
	        DTColumnBuilder.newColumn('full_name','Full Name').renderWith(function(data,type,full){
	        	return capitalize(`${full.full_name}`)
	        }),
	        DTColumnBuilder.newColumn('department','Department').renderWith(function(data){
	        	return capitalize(data)
	        }),
	        DTColumnBuilder.newColumn('email','Email'),
	        DTColumnBuilder.newColumn('status','Status').renderWith(function(data,type,full){
	        	return capitalize(data)
	        }),
	        DTColumnBuilder.newColumn('last_access_time','Last Interaction').renderWith(function(data){
	        	return dateFormatter.fromSQL(data).toFormat('dd MMM, yyyy \u2022 t')
	        }),
	        DTColumnBuilder.newColumn(null,'Actions').notSortable().renderWith(function(data){
	        	$scope.showFooter = true
	        	$scope.$apply()
	        	return "<a onclick='angular.element(this).scope().setModalFields(this)' data-row='"+JSON.stringify(data)+"' data-toggle='modal' href='#edit-employee' class='text-info table-link'><i class='fas fa-edit'></i> <span> View</span></a>"
	        })
	    ];
	    $timeout(function(){
			$('.add-employee-button').html('<button data-toggle="modal" data-target="#add-employee" class="btn btn-success float-right mb-3 mb-sm-0 header-text"><i class="fas fa-plus-square"></i> Add New Employee.</button>')
		})
	}
    $scope.setModalFields = function(event){
    	var data = $(event).data('row')
    	$scope.updateFields.id = data.employee_id
    	$scope.updateFields.fname = capitalize(data.first_name)
    	$scope.updateFields.lname = capitalize(data.last_name)
    	$scope.updateFields.email = data.email
    	$scope.updateFields.department = data.department_id
    	$scope.showButton.all = $scope.showButton.update = !(data.suspended.localeCompare('1') === 0 && data.password)
    	$scope.showButton.unemploy = data.employee_suspended.localeCompare('0') === 0
    	$scope.showButton.reemploy = data.employee_suspended.localeCompare('1') === 0
    	$scope.$apply()
    }
    $scope.addEmployee = async function($event){
    	$event.preventDefault()
    	if(!$scope.verifyData($scope.inputFields,$event)) return 
    	$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Employing . . .')
    	var response = await saveEmployeeDetails.employ($scope.inputFields)
    	$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('<i class="fas fa-pen"></i> Employ')
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			$scope.inputFields.fname = ''
			$scope.inputFields.lname = ''
			$scope.inputFields.email = ''
			$scope.inputFields.department = '0'
			$scope.tableInstance.reloadData()
    		$scope.$apply()
    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
    		return
    	}
    	$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
    }
    $scope.updateEmployee = async function($event){
    	$event.preventDefault()
    	if(!$scope.verifyData($scope.updateFields,$event) || !$scope.showButton.update) return
    	$($event.currentTarget).find('button.update').attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Updating . . .')
    	$scope.showButton.all = false
    	var response = await saveEmployeeDetails.update($scope.updateFields)
    	$($event.currentTarget).find('button.update').attr('disabled',false).removeClass('disabled').html('<i class="fas fa-edit"></i> Update')
    	$scope.showButton.all = true
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			$scope.tableInstance.reloadData()
    		$scope.$apply()
    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
    		return
    	}
    	if(parseInt(response.code) === 503){
    		$($event.currentTarget).parents('.modal').modal('hide')
			$scope.tableInstance.reloadData()
			$scope.$apply()
			$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-success').addClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Note: </strong>"+response.errors+"</div>").parent().toast('show')
			return
    	}
    	$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
    	$scope.$apply()
    }
    $scope.verifyData = function($object,$event){
    	reset_helper_texts()
    	for(var index in $object){
    		if(index === 'email' && !test_email($object.email)){
				change_helper_texts($($event.currentTarget).find($scope.errorSpan.email).parent().siblings('.helper-text'),'Email is of Invalid Format!','#dc3545')
    		}
    		if(!$object[index] || $object[index].trim().length < 1){
    			change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
    			return false;
    		}
    		if(index === 'department' || index === 'email' || index === 'id') continue
    		if(/[^a-z \'-]/i.test($object[index].trim())){
				change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This Field Contains Invalid Characters!','#dc3545')
    			return false;
			}
    	}
    	if(isNaN($object.department) || parseInt($object.department)<1){
    		change_helper_texts($($event.currentTarget).find($scope.errorSpan.department).parent().siblings('.helper-text'),'This is Required!','#dc3545')
    		return false
    	}
    	return true
    }
    $scope.unemploy = async function($event){
    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.unemploy){
    		$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
    		return
    	}
    	$scope.showButton.update = false
    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Unemploying . . .')
    	var response = await saveEmployeeDetails.unemploy($scope.updateFields)
    	$scope.showButton.update = true
    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-user-times"></i> Unemploy')
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			$scope.tableInstance.reloadData()
    		$scope.$apply()
    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
    		return
    	}
    	$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
    	$scope.$apply()
    }
    $scope.reemploy = async function($event){
    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.reemploy){
    		$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
    		return
    	}
    	$scope.showButton.update = false
    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Employing . . .')
    	var response = await saveEmployeeDetails.reemploy($scope.updateFields)
    	$scope.showButton.update = true
    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-user-check"></i> Reemploy')
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			$scope.tableInstance.reloadData()
    		$scope.$apply()
    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
    		return
    	}
    	$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
    	$scope.$apply()
    }
    $scope.showPrintModal = function(id){
    	$(id).modal('show')
    }
    $scope.renderTable()
}])
app.factory('saveEmployeeDetails',[function(){
	return {
		employ: function({fname,lname,email,department}){
			return $.ajax({
				url: `${base_url}owner/employ`,
				method: 'POST',
				dataType: 'json',
				data: {
					fname,
					lname,
					email,
					department
				}
			})
		},
		update: function({id,fname,lname,email,department}){
			return $.ajax({
				url: `${base_url}owner/update_employee_details`,
				method: 'POST',
				dataType: 'json',
				data: {
					id,
					fname,
					lname,
					email,
					department
				}
			})
		},
		unemploy: function({id}){
			return $.ajax({
				url: `${base_url}owner/unemploy_reemploy_employee`,
				method: 'POST',
				dataType: 'json',
				data: {
					id,
					action: 'unemploy'
				}
			})
		},
		reemploy: function({id}){
			return $.ajax({
				url: `${base_url}owner/unemploy_reemploy_employee`,
				method: 'POST',
				dataType: 'json',
				data: {
					id,
					action: 'reemploy'
				}
			})
		}
	}
}])