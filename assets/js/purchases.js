var app = angular.module('main')
app.controller('managePurchases',['$scope','DTOptionsBuilder','DTDefaultOptions','DTColumnBuilder','savePurchaseDetails', '$timeout', ($scope,DTOptionsBuilder,DTDefaultOptions,DTColumnBuilder,savePurchaseDetails,$timeout) => {
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
	$scope.checkoutFields = {
		item1: '',
		item2: '',
		item3: '',
		item4: '1'
	}
	$scope.updateFields = {
		id: '',
		item1: '0',
		item2: '',
		item3: '',
		item4: '',
		item5: '0'
	}
	$scope.errorSpan = {
		item1: 'input[name=item1]',
		item2: 'input[name=item2]',
		item3: 'input[name=item3]',
		item4: 'input[name=item4]'
	}
	$scope.cartErrorSpan = {
		item1: 'input[name=item1]',
		item2: 'input[name=item2]',
		item3: 'input[name=item3]',
		item4: 'select[name=item4]'
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
	$scope.reloadProducts = false
	$scope.excelHref = (navigator.language) ? `${base_url}pointofsale/download_purchase_details_spreadsheet/${navigator.language.replace('-','_')}` : `${base_url}pointofsale/download_product_details_spreadsheet`
	$scope.printHref = (navigator.language) ? `${base_url}pointofsale/print_purchase_details/${navigator.language.replace('-','_')}` : `${base_url}pointofsale/print_product_details`

	$scope.tableInstance = {}
	$scope.tableOptions = null;
	$scope.tableColumns = null

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
   		$scope.inputFields.item3 = ''
   		$scope.inputFields.item4 = ''
   		bounce('#cart-items')
   		$('#add-to-cart').modal('hide')
   	}

   	$scope.isNumeric = function($var){
   		return $var && !isNaN($var) && !isNaN(parseInt($var))
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

    $scope.formatCurrency = function(currency){
    	return formatNumberCurrency(currency, $scope.currencyCode)
    }

    $scope.checkout = async function($event){
    	if($scope.cartItems.length < 1 || !$scope.verifyCheckout($scope.checkoutFields, $event)) return;
    	$($event.currentTarget).find('button').attr('disabled',true).addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Checking Out . . .')
		let response = await savePurchaseDetails.addPurchases($scope.cartItems.map((element) => {
    		return {
    			product_id: element.item1.product_id,
    			quantity: element.item2,
    			total_cost: element.item3,
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
			try{
				$scope.tableInstance.reloadData()
			}catch(ex){}
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

    $scope.$watch('checkoutFields.item2 + checkoutFields.item1', () => {
    	if(isNaN($scope.checkoutFields.item2) || isNaN($scope.checkoutFields.item1) || isNaN(parseInt($scope.checkoutFields.item2)) || isNaN(parseInt($scope.checkoutFields.item1))){
    		$scope.checkoutFields.item3 = '-1'
    		return
    	}
    	$scope.checkoutFields.item3 = String(parseInt($scope.checkoutFields.item2) - parseInt($scope.checkoutFields.item1))
    })

    $scope.renderTable = function(){
		$scope.tableOptions = DTOptionsBuilder.fromSource(`${base_url}pointofsale/get_purchases`).withLanguage({
            "searchPlaceholder": "Search for Purchase . . ."
        })
		$scope.tableColumns = [
	        DTColumnBuilder.newColumn('product','Product').renderWith(function(data,type,full){
	        	return capitalize(data)
	        }),
	        DTColumnBuilder.newColumn('quantity','Quantity').renderWith(function(data){
	        	return capitalize(data)
	        }),
	        DTColumnBuilder.newColumn('total_cost','Total Cost').renderWith(function(data,type,full){
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
    	$scope.updateFields.id = data.purchase_id
    	$scope.updateFields.item1 = data.product_id
    	$scope.updateFields.item2 = data.quantity
    	$scope.updateFields.item3 = data.total_cost
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
		let response = await savePurchaseDetails.updatePurchase($scope.updateFields)
		$($event.currentTarget).find('button[type=submit]').attr('disabled',false).removeClass('disabled').html('<i class="fas fa-edit"></i> Update')
		$scope.showButton.all = true
		if(response.ok){
			$($event.currentTarget).parents('.modal').modal('hide')
			$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
			try{
				$scope.tableInstance.reloadData()
			}catch(ex){}
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
    	var response = await savePurchaseDetails.deactivateItem($scope.updateFields)
    	$scope.showButton.update = true
    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-times"></i> Disable')
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			try{
				$scope.tableInstance.reloadData()
			}catch(ex){}
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
    	var response = await savePurchaseDetails.reactivateItem($scope.updateFields)
    	$scope.showButton.update = true
    	$($event.currentTarget).attr('disabled',false).removeClass('disabled').html('<i class="fas fa-check"></i> Enable')
    	if(response.ok){
    		$($event.currentTarget).parents('.modal').modal('hide')
			try{
				$scope.tableInstance.reloadData()
			}catch(ex){}
			$scope.reloadProducts = true
    		$scope.$apply()
    		$('#site-info>div.toast>div.toast-body').removeClass('alert-info').addClass('alert-success').html("<div><i class='fas fa-exclamation-circle'><i><strong> Success: </strong>"+response.response+"</div>").parent().toast('show')
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
app.factory('savePurchaseDetails', ()=>{
	return {
		addPurchases: function(data){
			return $.ajax({
				url: `${base_url}pointofsale/add_purchases`,
				data: {data},
				dataType: 'json',
				method: 'POST'

			})

		},
		updatePurchase: function({id, item1, item2, item3, item4, item5}){
			return $.ajax({
				url: `${base_url}pointofsale/update_purchase`,
				data: {id, item1, item2, item3, item4, item5},
				dataType: 'json',
				method: 'POST'
			})
		},
		deactivateItem: function({id}){
			return $.ajax({
				url: `${base_url}pointofsale/disable_enable_purchase`,
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
				url: `${base_url}pointofsale/disable_enable_purchase`,
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