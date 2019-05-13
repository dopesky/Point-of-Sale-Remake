<?php 
$user_id = $this->session->userdata('userdata')['user_id'];
?>

<div class="row" ng-app="main" ng-cloak ng-controller="manageEmployees">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Manage Employees <i class="fas fa-users-cog"></i></h2>
		<div class="mt-4">
			<table datatable="" class="table table-striped table-hover w-100 data-table header-text" dt-options="tableOptions" dt-columns="tableColumns" dt-instance="tableInstance"></table>
		</div>
		<div ng-show="showFooter" class="mt-3 text-center box-shadow-inline">
			<a href="<?=site_url('owner/print_employee_details/'.$user_id)?>" ng-click="showPrintModal('#print-details')" target="print" class="btn btn-info mr-3"><i class="fas fa-print"></i> Print</a>
			<a href="<?=site_url('owner/download_employee_details_spreadsheet/'.$this->session->userdata('userdata')['user_id'])?>" class="btn btn-success"><i class="fas fa-file-csv"></i> Excel</a>
		</div>
	</main>
	<div class="modal fade text-dark" id="add-employee">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-plus-square"></i> New Employee</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addEmployee($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user"></i></div>
										</div>
										<input type="text" name="fname" ng-model="inputFields.fname" class="form-control" placeholder="First Name">
									</div>
									<span class="helper-text text-left" data-original="e.g John"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
										</div>
										<input type="text" name="lname" ng-model="inputFields.lname" class="form-control" placeholder="Last Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Doe"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-at"></i></div>
										</div>
										<input type="email" name="email" ng-model="inputFields.email" class="form-control" placeholder="Email">
									</div>
									<span class="helper-text text-left" data-original="e.g dopesky@example.com"></span>
								</div>
								<?php if($departments){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-tools"></i></div>
											</div>
										    <select class="form-control" ng-model="inputFields.department" name="department">
										    	<option value="0" disabled>Department</option>
										    	<?php foreach($departments as $department){?>
										    		<option value="<?=$department->department_id?>"><?=ucfirst($department->department)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Department For This Employee!"></span>
									</div>
								<?php }?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info width-100"><i class="fas fa-pen"></i> Employ</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="edit-employee">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-cog fa-spin"></i> Modify Employee Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="updateEmployee($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user"></i></div>
										</div>
										<input type="text" name="fname" ng-model="updateFields.fname" class="form-control" placeholder="First Name">
									</div>
									<span class="helper-text text-left" data-original="e.g John"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
										</div>
										<input type="text" name="lname" ng-model="updateFields.lname" class="form-control" placeholder="Last Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Doe"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-at"></i></div>
										</div>
										<input type="email" name="email" ng-model="updateFields.email" class="form-control" placeholder="Email">
									</div>
									<span class="helper-text text-left" data-original="e.g dopesky@example.com"></span>
								</div>
								<?php if($departments){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-tools"></i></div>
											</div>
										    <select class="form-control" ng-model="updateFields.department" name="department">
										    	<option value="0" disabled>Department</option>
										    	<?php foreach($departments as $department){?>
										    		<option value="<?=$department->department_id?>"><?=ucfirst($department->department)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Department For This Employee!"></span>
									</div>
								<?php }?>
								<div class="form-group row">
									<div class="col-12 col-sm-6 text-left">
										<button ng-show="showButton.update" type="submit" class="btn btn-info update mt-3 mr-3"><i class="fas fa-edit"></i> Update</button>
									</div>
									<div class="col-12 col-sm-6 text-right">
										<button ng-show="showButton.unemploy && showButton.all" ng-click="unemploy($event)" type="button" class="btn btn-danger mt-3"><i class="fas fa-user-times"></i> Unemploy</button>
										<button ng-show="showButton.reemploy && showButton.all" ng-click="reemploy($event)" type="button" class="btn btn-success mt-3"><i class="fas fa-user-check"></i> Reemploy</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="print-details">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-print"></i> Print Employee Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body p-1 h-80">
					<iframe class="w-100 h-100" src="" name="print"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var user_id = "<?=$user_id?>"
	var app = angular.module('main', ['datatables'])
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
		        DTColumnBuilder.newColumn('first_name','Full Name').renderWith(function(data,type,full){
		        	return capitalize(`${full.first_name} ${full.last_name}`)
		        }),
		        DTColumnBuilder.newColumn('department','Department').renderWith(function(data){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn('email','Email'),
		        DTColumnBuilder.newColumn('active','Status').renderWith(function(data,type,full){
		        	var status = (full.suspended.localeCompare('1') === 0 && !full.password) ? 'Awaiting Verification': (full.suspended.localeCompare('1') === 0 && full.password) ? 'Account Suspended' : (full.active.localeCompare('1') === 0) ? 'Active' : 'Unemployed'
		        	return status
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
	    	$scope.showButton.unemploy = data.active.localeCompare('1') === 0
	    	$scope.showButton.reemploy = data.active.localeCompare('0') === 0
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
</script>