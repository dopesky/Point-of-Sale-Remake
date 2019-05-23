<div class="row" ng-app="main" ng-cloak ng-controller="manageProducts">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Manage Products <i class="fas fa-shopping-cart"></i></h2>
		<div class="mt-4">
			<table datatable="" class="table table-striped table-hover w-100 data-table header-text" dt-options="tableOptions" dt-columns="tableColumns" dt-instance="tableInstance"></table>
		</div>
		<div ng-show="showFooter" class="mt-3 text-center box-shadow-inline">
			<a href="<?=site_url('owner/print_employee_details')?>" ng-click="showPrintModal('#print-details')" target="print" class="btn btn-info mr-3"><i class="fas fa-print"></i> Print</a>
			<a href="<?=site_url('owner/download_product_details_spreadsheet')?>" class="btn btn-success"><i class="fas fa-file-csv"></i> Excel</a>
		</div>
	</main>
	<div class="modal fade text-dark" id="add-item">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-plus-square"></i> New Product</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addItem($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-bag"></i></div>
										</div>
										<input type="text" name="item1" ng-model="inputFields.item1" class="form-control" placeholder="Product Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Computer"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
										</div>
										<input type="text" name="item2" ng-model="inputFields.item2" class="form-control" placeholder="Cost Per Unit">
									</div>
									<span class="helper-text text-left" data-original="e.g 24"></span>
								</div>
								<?php if($categories){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-shopping-basket"></i></div>
											</div>
										    <select class="form-control" ng-model="inputFields.item3" name="item3">
										    	<option value="0" disabled>Category</option>
										    	<?php foreach($categories as $category){?>
										    		<option value="<?=$category->category_id?>"><?=ucfirst($category->category_name)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Category For This Product!"></span>
									</div>
								<?php }?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info width-100"><i class="fas fa-pen"></i> Add</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="edit-item">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-cog fa-spin"></i> Modify Product Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="updateItem($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-bag"></i></div>
										</div>
										<input type="text" name="item1" ng-model="updateFields.item1" class="form-control" placeholder="Product Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Computer"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
										</div>
										<input type="text" name="item2" ng-model="updateFields.item2" class="form-control" placeholder="Cost Per Unit">
									</div>
									<span class="helper-text text-left" data-original="e.g 24"></span>
								</div>
								<?php if($categories){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-shopping-basket"></i></div>
											</div>
										    <select class="form-control" ng-model="updateFields.item3" name="item3">
										    	<option value="0" disabled>Category</option>
										    	<?php foreach($categories as $category){?>
										    		<option value="<?=$category->category_id?>"><?=ucfirst($category->category_name)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Category For This Product!"></span>
									</div>
								<?php }?>
								<div class="form-group row">
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.update" type="submit" class="btn btn-info update mt-3 btn-block"><i class="fas fa-edit"></i> Update</button>
									</div>
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.disable && showButton.all" ng-click="deactivateItem($event)" type="button" class="btn btn-danger mt-3 btn-block"><i class="fas fa-times"></i> Disable</button>
										<button ng-show="showButton.enable && showButton.all" ng-click="reactivateItem($event)" type="button" class="btn btn-success mt-3 btn-block"><i class="fas fa-check"></i> Enable</button>
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
					<h4 class="text-muted"><i class="fas fa-print"></i> Print Product Details</h4>
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
	var app = angular.module('main')
	app.controller('manageProducts',['$scope','DTOptionsBuilder','DTDefaultOptions','DTColumnBuilder','saveProductDetails', '$timeout', function($scope,DTOptionsBuilder,DTDefaultOptions,DTColumnBuilder,saveProductDetails,$timeout){
		$scope.inputFields = {
			item1: '',
			item2: '',
			item3: '0',
		}
		$scope.updateFields = {
			id: '',
			item1: '',
			item2: '',
			item3: '0'
		}
		$scope.errorSpan = {
			item1: 'input[name=item1]',
			item2: 'input[name=item2]',
			item3: 'select[name=item3]'
		}
		$scope.showButton = {
			all: true,
			update: true,
			disable: false,
			enable: false
		}
		$scope.showFooter = false
		$scope.tableInstance = {};
		$scope.renderTable = function(){
			DTDefaultOptions.setDisplayLength(25).setDOM('<"row"<"col-sm-6 add-item-button"><"col-sm-6 d-flex justify-content-sm-start justify-content-end"f>>rtp').setLanguage({
                "paginate": {
                    "next": '<i class="fas fa-forward" aria-hidden="true"></i>',
                    "previous": '<i class="fas fa-backward" aria-hidden="true"></i>'
                },
                "search": "<div class='input-group'>_INPUT_<div class='input-group-append'><div class='input-group-text'><i class='fas fa-search'></i></div></div></div>",
                "searchPlaceholder": "Search for Product . . ."
            }).setOption('responsive',true)
			$scope.tableOptions = DTOptionsBuilder.fromSource(`${base_url}owner/get_products`)
			$scope.tableColumns = [
		        DTColumnBuilder.newColumn('product','Product').renderWith(function(data,type,full){
		        	return capitalize(`${data}`)
		        }),
		        DTColumnBuilder.newColumn('category_name','Category').renderWith(function(data){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn('cost_per_unit','Cost Per Unit'),
		        DTColumnBuilder.newColumn('status','Status'),
		        DTColumnBuilder.newColumn('modified_date','Last Change').renderWith(function(data){
		        	return dateFormatter.fromSQL(data).toFormat('dd MMM, yyyy \u2022 t')
		        }),
		        DTColumnBuilder.newColumn(null,'Actions').notSortable().renderWith(function(data){
		        	$scope.showFooter = true
		        	$scope.$apply()
		        	return "<a onclick='angular.element(this).scope().setModalFields(this)' data-row='"+JSON.stringify(data)+"' data-toggle='modal' href='#edit-item' class='text-info table-link'><i class='fas fa-edit'></i> <span> View</span></a>"
		        })
		    ];
		    $timeout(function(){
				$('.add-item-button').html('<button data-toggle="modal" data-target="#add-item" class="btn btn-success float-right mb-3 mb-sm-0 header-text"><i class="fas fa-plus-square"></i> Add New Product.</button>')
			})
		}
	    $scope.setModalFields = function(event){
	    	var data = $(event).data('row')
	    	$scope.updateFields.id = data.product_id
	    	$scope.updateFields.item1 = capitalize(data.product)
	    	$scope.updateFields.item2 = capitalize(data.cost_per_unit)
	    	$scope.updateFields.item3 = data.category_id
	    	$scope.showButton.all = $scope.showButton.update = (data.suspended.localeCompare('0') === 0 && data.owner_suspended.localeCompare('0') === 0 && data.owner_active.localeCompare('1') === 0)
	    	$scope.showButton.disable = data.active.localeCompare('1') === 0
	    	$scope.showButton.enable = data.active.localeCompare('0') === 0
	    	$scope.$apply()
	    }
	    $scope.addItem = async function($event){
	    	$event.preventDefault()
	    	if(!$scope.verifyData($scope.inputFields,$event)) return 
	    	$($event.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Adding . . .')
	    	var response = await saveProductDetails.addItem($scope.inputFields)
	    	$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('<i class="fas fa-pen"></i> Add')
	    	if(response.ok){
	    		$($event.currentTarget).parents('.modal').modal('hide')
				$scope.inputFields.item1 = ''
				$scope.inputFields.item2 = ''
				$scope.inputFields.item3 = '0'
				$scope.tableInstance.reloadData()
	    		$scope.$apply()
	    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
	    		return
	    	}
	    	$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
	    }
	    $scope.updateItem = async function($event){
	    	$event.preventDefault()
	    	if(!$scope.verifyData($scope.updateFields,$event) || !$scope.showButton.update) return
	    	$($event.currentTarget).find('button.update').attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Updating . . .')
	    	$scope.showButton.all = false
	    	var response = await saveProductDetails.updateItem($scope.updateFields)
	    	$($event.currentTarget).find('button.update').attr('disabled',false).removeClass('disabled').html('<i class="fas fa-edit"></i> Update')
	    	$scope.showButton.all = true
	    	if(response.ok){
	    		$($event.currentTarget).parents('.modal').modal('hide')
				$scope.tableInstance.reloadData()
	    		$scope.$apply()
	    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
	    		return
	    	}
	    	$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
	    	$scope.$apply()
	    }
	    $scope.verifyData = function($object,$event){
	    	reset_helper_texts()
	    	for(var index in $object){
	    		if(!$object[index] || $object[index].trim().length < 1){
	    			change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
	    			return false;
	    		}
	    		if(index === 'id') continue
	    		if(index === 'item2' || index === 'item3'){
	    			if(isNaN($object[index]) || parseInt($object[index]) < 1){
	    				if(index === 'item3'){
	    					change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
	    				}else{
	    					change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This Should Be a Number!','#dc3545')
	    				}
	    				return false
	    			}
	    			continue
	    		}
	    		if(/[^a-z0-9 \'-]/i.test($object[index].trim())){
					change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This Field Contains Invalid Characters!','#dc3545')
	    			return false;
				}
	    	}
	    	return true
	    }
	    $scope.deactivateItem = async function($event){
	    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.disable){
	    		$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
	    		return
	    	}
	    	$scope.showButton.update = false
	    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Disabling . . .')
	    	var response = await saveProductDetails.deactivateItem($scope.updateFields)
	    	$scope.showButton.update = true
	    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-times"></i> Disable')
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
	    $scope.reactivateItem = async function($event){
	    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.enable){
	    		$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
	    		return
	    	}
	    	$scope.showButton.update = false
	    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Enabling . . .')
	    	var response = await saveProductDetails.reactivateItem($scope.updateFields)
	    	$scope.showButton.update = true
	    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-check"></i> Enable')
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
	app.factory('saveProductDetails',[function(){
		return {
			addItem: function({item1,item2,item3}){
				return $.ajax({
					url: `${base_url}owner/add_product`,
					method: 'POST',
					dataType: 'json',
					data: {
						item1,
						item2,
						item3
					}
				})
			},
			updateItem: function({id,item1,item2,item3}){
				return $.ajax({
					url: `${base_url}owner/update_product_details`,
					method: 'POST',
					dataType: 'json',
					data: {
						id,
						item1,
						item2,
						item3
					}
				})
			},
			deactivateItem: function({id}){
				return $.ajax({
					url: `${base_url}owner/activate_deactivate_product`,
					method: 'POST',
					dataType: 'json',
					data: {
						id,
						action: 'deactivate'
					}
				})
			},
			reactivateItem: function({id}){
				return $.ajax({
					url: `${base_url}owner/activate_deactivate_product`,
					method: 'POST',
					dataType: 'json',
					data: {
						id,
						action: 'reactivate'
					}
				})
			}
		}
	}])
</script>