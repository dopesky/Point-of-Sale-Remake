<div class="row" ng-app="main" ng-cloak ng-controller="manageSales" ng-init="currencyCode = '<?=$user_details->currency_code?>'">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Sales <i class="fas fa-cart-arrow-down"></i></h2>
		<div class="box-shadow p-0 pt-0 container">
			<ul class="nav nav-tabs nav-justified tabs rounded-0">
				<li class="nav-item">
				    <a class="nav-link active" data-toggle="tab" href="#view-products"><i class="fas fa-shopping-cart"></i> <span class="d-none d-sm-initial">Products</span></a>
				</li>
				<li class="nav-item">
				    <a class="nav-link d-flex justify-content-center align-items-center" data-toggle="tab" href="#shopping-cart"><span><i class="fas fa-cart-plus"></i> <span class="d-none d-sm-initial">Cart</span></span> <sup id="cart-items" ng-show="cartItems.length" class="badge badge-info font-sm ml-1">{{cartItems.length}}</sup></a>
				</li>
				<?php if($this->session->userdata('userdata') !== null && (int)$this->session->userdata('userdata')['level'] > 1){?>
					<li class="nav-item">
					    <a class="nav-link" data-toggle="tab" href="#manage-purchases"><i class="fas fa-toolbox"></i> <span class="d-none d-sm-initial">Manage</span></a>
					</li>
				<?php }?>
			</ul>
			<div class="tab-content limit-tab-content mb-3">
				<div class="tab-pane p-1 p-sm-3 active" id="view-products">
					<view-products url="<?=base_url('pointofsale/get_products_for_sale')?>" type="sale"></view-products>
				</div>
				<div class="tab-pane p-3 fade" id="shopping-cart">
					<view-cart type="sale"></view-cart>
				</div>
				<?php if($this->session->userdata('userdata') !== null && (int)$this->session->userdata('userdata')['level'] > 1){?>
					<div class="tab-pane p-3 fade" id="manage-purchases">
						<?=$this->load->view('manage_sales',array(),true)?>
					</div>
				<?php }?>
			</div>
		</div>
	</main>
	<div class="modal fade text-muted" id="add-to-cart">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="header-text"><i class="fas fa-cart-plus"></i> {{updateCart && 'Update Cart' || 'Add to Cart'}}</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addToCart($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-cart"></i></div>
										</div>
										<input type="text" name="item1" ng-model="inputFields.item1.product" class="form-control" placeholder="Product" readonly>
									</div>
									<span class="helper-text text-left" data-original="Product Name is Auto-Filled from your Selection."></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></div>
										</div>
										<input type="text" name="item3" ng-readonly="true" ng-model="inputFields.item3" class="form-control" placeholder="Total Cost (after discount)">
									</div>
									<span class="helper-text text-left" data-original="Total Price is Auto-Calculated from your Selection"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-list-ol"></i></div>
										</div>
										<input type="text" name="item2" ng-model="inputFields.item2" class="form-control" placeholder="Quantity">
									</div>
									<span class="helper-text text-left" data-original="Quantity e.g 3"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
										</div>
										<input type="text" name="item4" ng-model="inputFields.item4" class="form-control" placeholder="Discount">
									</div>
									<span class="helper-text text-left" data-original="Discount Allowed e.g 11"></span>
								</div>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info"><i class="fas fa-cart-plus"></i> {{updateCart && 'Update Cart' || 'Add to Cart'}}</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-muted" id="checkout-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="header-text"><i class="fas fa-cart-plus"></i> Checkout</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="checkout($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></div>
										</div>
										<input type="text" name="item1" ng-model="checkoutFields.item1" class="form-control" placeholder="Total Cost" readonly>
									</div>
									<span class="helper-text text-left" data-original="Total Cost of Items in the Cart!"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-wallet"></i></div>
										</div>
										<input type="text" name="item2" ng-model="checkoutFields.item2" class="form-control" placeholder="Amount Paid">
									</div>
									<span class="helper-text text-left" data-original="Amount Paid for Items in Cart!"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-coins"></i></div>
										</div>
										<input type="text" name="item3" ng-model="checkoutFields.item3" class="form-control" placeholder="Balance" readonly>
									</div>
									<span class="helper-text text-left" data-original="Balance After Payment!"></span>
								</div>
								<?php if($payment_methods):?>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-credit-card"></i></div>						
											</div>
											<select ng-model="checkoutFields.item4" class="form-control" name="item4">
												<?php foreach($payment_methods as $method):?>
													<option value="<?=$method->method_id?>"><?=ucwords($method->method)?></option>
												<?php endforeach;?>
											</select>
										</div>
										<span class="helper-text text-left" data-original="Method Used to Pay for the Items."></span>
									</div>
								<?php endif;?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info"><i class="fas fa-sign-out-alt"></i> Checkout</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var app = angular.module('main')
	app.controller('manageSales',['$scope','DTOptionsBuilder','DTDefaultOptions','DTColumnBuilder','saveSalesDetails', '$timeout', ($scope,DTOptionsBuilder,DTDefaultOptions,DTColumnBuilder,saveSalesDetails,$timeout) => {
		DTDefaultOptions.setDisplayLength(25).setDOM('<"d-flex justify-content-end"f>rtp').setLanguage({
            "paginate": {
                "next": '<i class="fas fa-forward" aria-hidden="true"></i>',
                "previous": '<i class="fas fa-backward" aria-hidden="true"></i>'
            },
            "search": "<div class='input-group data-table-input'>_INPUT_<div class='input-group-append'><div class='input-group-text'><i class='fas fa-search'></i></div></div></div>",
            "searchPlaceholder": "Search in Cart . . ."
        }).setOption('responsive',true)

		$scope.cartItems = []

		$scope.inputFields = {
			item1: {},
			item2: '',
			item3: '',
			item4: ''
		}
		$scope.errorSpan = {
			item1: 'input[name=item1]',
			item2: 'input[name=item2]',
			item3: 'input[name=item3]',
			item4: 'input[name=item4]'
		}
		$scope.checkoutFields = {
			item1: '',
			item2: '',
			item3: '',
			item4: '1'
		}
		$scope.cartErrorSpan = {
			item1: 'input[name=item1]',
			item2: 'input[name=item2]',
			item3: 'input[name=item3]',
			item4: 'select[name=item4]'
		}
		$scope.updateFields = {
			id: '',
			item1: '0',
			item2: '',
			item3: '',
			item4: '',
			item5: '0'
		}
		$scope.updateErrorSpan = {
			id: '',
			item1: 'select[name=item1]',
			item2: 'input[name=item2]',
			item3: 'input[name=item3]',
			item4: 'input[name=item4]',
			item5: 'select[name=item5]'
		}

		$scope.updateCart = false
		$scope.showFooter = false
		$scope.showButton = {
			all: true,
			update: true,
			disable: false,
			enable: false
		}

		$scope.excelHref = (navigator.language) ? `${base_url}pointofsale/download_sale_details_spreadsheet/${navigator.language.replace('-','_')}` : `${base_url}pointofsale/download_product_details_spreadsheet`
		$scope.printHref = (navigator.language) ? `${base_url}pointofsale/print_sale_details/${navigator.language.replace('-','_')}` : `${base_url}pointofsale/print_product_details`

		$scope.reloadProducts = false

		$scope.tableInstance = {}
		$scope.tableOptions = null;
		$scope.tableColumns = null

		$scope.formatCurrency = function(currency){
	    	return formatNumberCurrency(currency, $scope.currencyCode)
	    }

	    $scope.isNumeric = function($var){
       		return $var && !isNaN($var) && !isNaN(parseInt($var))
       	}

       	$scope.showPrintModal = function(id){
	    	$(id).modal('show')
	    }

       	$scope.addToCart = function($event){
       		let found = false;
       		if(!$scope.verifyData($scope.inputFields, $event)) return;
       		for (var i = 0; i < $scope.cartItems.length; i++) {
       			if($scope.cartItems[i].item1.product_id.localeCompare($scope.inputFields.item1.product_id) === 0){
       				if($scope.updateCart){
       					$scope.cartItems[i].item2 = $.extend({}, $scope.inputFields).item2
       					$scope.cartItems[i].item3 = $.extend({}, $scope.inputFields).item3
       					$scope.cartItems[i].item4 = $.extend({}, $scope.inputFields).item4
       				}else{
       					$scope.cartItems[i].item2 = String(parseInt($scope.cartItems[i].item2) + parseInt($.extend({}, $scope.inputFields).item2))
       					$scope.cartItems[i].item3 = String(parseInt($scope.cartItems[i].item3) + parseInt($.extend({}, $scope.inputFields).item3))
           				$scope.cartItems[i].item4 = String(parseInt($scope.cartItems[i].item4) + parseInt($.extend({}, $scope.inputFields).item4))
       				}
       				found = true;
       				break;
       			}
       		}
       		if(!found){
       			$scope.cartItems.push($.extend({}, $scope.inputFields))
       		}
       		$scope.inputFields.item1 = {}
       		$scope.inputFields.item2 = ''
       		$scope.inputFields.item4 = ''
       		bounce('#cart-items')
       		$('#add-to-cart').modal('hide')
       	}

       	$scope.verifyData = function($object,$event){
	    	reset_helper_texts()
	    	for(var index in $object){
	    		if(index === 'item1'){
    				if($object[index].product_id === undefined){
    					change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'A Serious Error has Occurred!','#dc3545')
    					return false;
    				}
    				continue
    			}

    			$object['item4'] = (!$object['item4'] || $object['item4'].trim().length < 1) ? '0' : $object['item4']

	    		if(!$object[index] || $object[index].trim().length < 1){
	    			change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
	    			return false;
	    		}

	    		if(isNaN($object[index]) || parseInt($object[index]) < 0){
    				change_helper_texts($($event.currentTarget).find($scope.errorSpan[index]).parent().siblings('.helper-text'),'This Should Be a Number Greater than 0!','#dc3545')
    				return false
    			}
	    	}
	    	return true
	    }

	    $scope.checkout = async function($event){
	    	if($scope.cartItems.length < 1 || !$scope.verifyCheckout($scope.checkoutFields, $event)) return;
	    	$($event.currentTarget).find('button').attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Checking Out . . .')
			let response = await saveSalesDetails.saveSales($scope.cartItems.map((element) => {
	    		return {
	    			product_id: element.item1.product_id,
	    			quantity: element.item2,
	    			discount: element.item4,
	    			method_id: $scope.checkoutFields.item4
	    		}
	    	}))
			$($event.currentTarget).find('button').attr('disabled',false).removeClass('disabled').html('<i class="fas fa-sign-out-alt"></i> Checkout')
			if(response.ok){
				$($event.currentTarget).parents('.modal').modal('hide')
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				$scope.cartItems = []
				$scope.inputFields = {
					item1: {},
					item2: '',
					item3: '',
					item4: ''
				}
				$scope.checkoutFields = {
					item1: '',
					item2: '',
					item3: '',
					item4: '1'
				}
				$scope.updateCart = false
				$scope.tableInstance.reloadData()
				$scope.reloadProducts = true
				$scope.$apply()
			}else{
				$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			}
	    }

	    $scope.verifyCheckout = function($object,$event){
	    	reset_helper_texts()
	    	for(var index in $object){
	    		if(!$object[index] || $object[index].trim().length < 1){
	    			change_helper_texts($($event.currentTarget).find($scope.cartErrorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
	    			return false;
	    		}

	    		if(isNaN($object[index]) || parseInt($object[index]) < 0){
    				change_helper_texts($($event.currentTarget).find($scope.cartErrorSpan[index]).parent().siblings('.helper-text'),'This Should Be a Number Greater than 0!','#dc3545')
    				return false
    			}
	    	}
	    	return true
	    }

	    $scope.renderTable = function(){
			$scope.tableOptions = DTOptionsBuilder.fromSource(`${base_url}pointofsale/get_sales`).withLanguage({
	            "searchPlaceholder": "Search for Sale . . ."
	        })
			$scope.tableColumns = [
		        DTColumnBuilder.newColumn('product','Product').renderWith(function(data,type,full){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn('quantity','Quantity').renderWith(function(data){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn('cost_per_item','Unit Cost').renderWith(function(data,type,full){
		        	return formatNumberCurrency(data, $scope.currencyCode)
		        }),
		        DTColumnBuilder.newColumn('discount','Discount').renderWith(function(data,type,full){
		        	return formatNumberCurrency(data, $scope.currencyCode)
		        }),
		        DTColumnBuilder.newColumn('method','Paid Via').renderWith(function(data,type,full){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn('recorder_name','Recorder').renderWith(function(data){
		        	return capitalize(data)
		        }).withClass('none'),
		        DTColumnBuilder.newColumn('modifier_name','Modifier').renderWith(function(data){
		        	return capitalize(data)
		        }).withClass('none'),
		        DTColumnBuilder.newColumn('modified_date','Last Modified').renderWith(function(data){
		        	return dateFormatter.fromSQL(data).toFormat('dd MMM, yyyy \u2022 t')
		        }).withClass('none'),
		        DTColumnBuilder.newColumn('status','Status').renderWith(function(data,type,full){
		        	return capitalize(data)
		        }),
		        DTColumnBuilder.newColumn(null,'Actions').notSortable().renderWith(function(data){
		        	$scope.showFooter = true
		        	$scope.$apply()
		        	return "<a onclick='angular.element(this).scope().setModalFields(this)' data-row='"+JSON.stringify(data)+"' data-toggle='modal' href='#edit-item' class='text-info table-link'><i class='fas fa-edit'></i> <span> View</span></a>"
		        })
		    ];
		}

		$scope.setModalFields = function(event){
			var data = $(event).data('row')
	    	$scope.updateFields.id = data.sale_id
	    	$scope.updateFields.item1 = data.product_id
	    	$scope.updateFields.item2 = data.quantity
	    	$scope.updateFields.item3 = data.cost_per_item
	    	$scope.updateFields.item4 = data.discount
	    	$scope.updateFields.item5 = data.method_id
	    	$scope.showButton.all = $scope.showButton.update = (data.suspended.localeCompare('0') === 0 && data.owner_suspended.localeCompare('0') === 0 && data.owner_active.localeCompare('1') === 0)
	    	$scope.showButton.disable = data.active.localeCompare('1') === 0
	    	$scope.showButton.enable = data.active.localeCompare('0') === 0
	    	$scope.$apply()
		}

		$scope.verifyEditData = function($object,$event){
	    	reset_helper_texts()
	    	for(var index in $object){
    			$object['item4'] = (!$object['item4'] || $object['item4'].trim().length < 1) ? '0' : $object['item4']

	    		if(!$object[index] || $object[index].trim().length < 1){
	    			change_helper_texts($($event.currentTarget).find($scope.updateErrorSpan[index]).parent().siblings('.helper-text'),'This is Required!','#dc3545')
	    			return false;
	    		}

	    		if(!$scope.isNumeric($object[index]) || parseInt($object[index]) < 0){
    				change_helper_texts($($event.currentTarget).find($scope.updateErrorSpan[index]).parent().siblings('.helper-text'),'This Should Be a Number Greater than 0!','#dc3545')
    				return false
    			}
	    	}
	    	return true
	    }

	    $scope.updateItem = async function($event){
	    	$event.preventDefault()
	    	if(!$scope.verifyEditData($scope.updateFields, $event) || !$scope.showButton.update) return
	    	$($event.currentTarget).find('button[type=submit]').attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Updating . . .')
	    	$scope.showButton.all = false
			let response = await saveSalesDetails.updateSale($scope.updateFields)
			$($event.currentTarget).find('button[type=submit]').attr('disabled',false).removeClass('disabled').html('<i class="fas fa-edit"></i> Update')
			$scope.showButton.all = true
			if(response.ok){
				$($event.currentTarget).parents('.modal').modal('hide')
				$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
				$scope.tableInstance.reloadData()
				$scope.reloadProducts = true
				$scope.$apply()
			}else{
				$($event.currentTarget).find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
				$scope.$apply()
			}
	    }

	    $scope.deactivateItem = async function($event){
	    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.disable){
	    		$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
	    		return
	    	}
	    	$scope.showButton.update = false
	    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Disabling . . .')
	    	var response = await saveSalesDetails.deactivateItem($scope.updateFields)
	    	$scope.showButton.update = true
	    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-times"></i> Disable')
	    	if(response.ok){
	    		$($event.currentTarget).parents('.modal').modal('hide')
				$scope.tableInstance.reloadData()
				$scope.reloadProducts = true
	    		$scope.$apply()
	    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
	    		return
	    	}
	    	$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
	    	$scope.$apply()
	    }

	    $scope.reactivateItem = async function($event){
	    	if(!$scope.updateFields.id || !$scope.showButton.all || !$scope.showButton.enable){
	    		$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>You Cannot Perform This Action. Try Again!</div>").parent().toast({delay:5000}).toast('show')
	    		return
	    	}
	    	$scope.showButton.update = false
	    	$($event.currentTarget).attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Enabling . . .')
	    	var response = await saveSalesDetails.reactivateItem($scope.updateFields)
	    	$scope.showButton.update = true
	    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-check"></i> Enable')
	    	if(response.ok){
	    		$($event.currentTarget).parents('.modal').modal('hide')
				$scope.tableInstance.reloadData()
				$scope.reloadProducts = true
	    		$scope.$apply()
	    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
	    		return
	    	}
	    	$($event.currentTarget).parents('form').find('.form-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
	    	$scope.$apply()
	    }

	    $scope.$watch('checkoutFields.item2 + checkoutFields.item1', () => {
	    	if(isNaN($scope.checkoutFields.item2) || isNaN($scope.checkoutFields.item1) || isNaN(parseInt($scope.checkoutFields.item2)) || isNaN(parseInt($scope.checkoutFields.item1))){
	    		$scope.checkoutFields.item3 = '-1'
	    		return
	    	}
	    	$scope.checkoutFields.item3 = String(parseInt($scope.checkoutFields.item2) - parseInt($scope.checkoutFields.item1))
	    })

	    $scope.$watch('inputFields.item2 + inputFields.item4', (newValue) => {
       		if(!$scope.inputFields || $scope.inputFields.item1.product_id === undefined) return
       		if(isNaN($scope.inputFields.item2) || parseInt($scope.inputFields.item2) < 0 || isNaN($scope.inputFields.item4) || parseInt($scope.inputFields.item4) < 0){
       			$scope.inputFields.item3 = '-1'
       		}else{
       			let newVal = isNaN(parseInt($scope.inputFields.item2)) ? 0 : parseInt($scope.inputFields.item2), 
       			disc = isNaN(parseInt($scope.inputFields.item4)) ? 0 : parseInt($scope.inputFields.item4)
       			$scope.inputFields.item3 = String(parseInt($scope.inputFields.item1.cost_per_unit) * newVal - disc)
       		}
       	})
		$scope.renderTable()
	}])
	app.factory('saveSalesDetails', ()=>{
		return {
			saveSales: function(data){
				return $.ajax({
					url: `${base_url}pointofsale/add_sales`,
					data: {data},
					dataType: 'json',
					method: 'POST'
				})
			},
			updateSale: function({id, item1, item2, item3, item4, item5}){
				return $.ajax({
					url: `${base_url}pointofsale/update_sale`,
					data: {id, item1, item2, item3, item4, item5},
					dataType: 'json',
					method: 'POST'
				})
			},
			deactivateItem: function({id}){
				return $.ajax({
					url: `${base_url}pointofsale/disable_enable_sale`,
					data: {
						id, 
						action: 'disable'
					},
					dataType: 'json',
					method: 'POST'
				})
			},
			reactivateItem: function({id}){
				return $.ajax({
					url: `${base_url}pointofsale/disable_enable_sale`,
					data: {
						id, 
						action: 'enable'
					},
					dataType: 'json',
					method: 'POST'
				})
			}
		}
	})
</script>