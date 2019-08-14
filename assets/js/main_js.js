
function loginLogic(form){
	form.preventDefault()

	var username = $(form.currentTarget).find('input[name=username]').val().trim(),
	password = $(form.currentTarget).find('input[name=password]').val()

	if(!validate_login_details(username,password)) return

	$(form.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Signing in . . .')
	$.ajax({
		url: $(form.currentTarget).attr('action'),
		data: {
			username: username,
			password: password
		},
		dataType: 'json',
		method: 'POST'
	}).then(response=>{
		if(!response.ok){
			$('#login-button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Login')
			$('#login-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			return
		}
		window.location.reload(true)
	})
}

function validate_login_details(username,password){
	reset_helper_texts()
	if(!hasContent(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Email is required!','#dc3545')
		return false
	}
	if(!test_email(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Email is of invalid format!','#dc3545')
		return false
	}

	if(!hasContent(password.trim())){
		$('input[name=password]').val('')
		change_helper_texts($('input[name=password]').parent().siblings('.helper-text'),'Password is required!','#dc3545')
		return false
	}
	if(password.length<8){
		change_helper_texts($('input[name=password]').parent().siblings('.helper-text'),'Password must be atleast 8 characters!','#dc3545')
		return false
	}
	return true;
}

function signUpLogic(form,button,html,spinner){
	form.preventDefault()

	var username = $(form.currentTarget).find('input[name=username]').val().trim()

	reset_helper_texts()

	if(!hasContent(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Email is required!','#dc3545')
		return
	}

	if(!test_email(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Email is of invalid format!','#dc3545')
		return
	}

	$(form.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> '+spinner)
	$.ajax({
		url: $(form.currentTarget).attr('action'),
		data: {
			username: username
		},
		dataType: 'json',
		method: 'POST'
	}).then(response=>{
		if(!response.ok){
			$(button).attr('disabled',false).removeClass('disabled').addClass('width-100').html(html)
			if(response.code === 503){
				$('#page-errors>div.toast-body').removeClass('alert-danger').addClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			}else{
				$('#page-errors>div.toast-body').addClass('alert-danger').removeClass('alert-warning').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			}
			return
		}
		window.location.assign(`${base_url}`)
	})
}

function changePasswordLogic(form){
	form.preventDefault()

	var newPassword = $(form.currentTarget).find('input[name=new_password]').val()
	var repeatedPassword = $(form.currentTarget).find('input[name=repeat_password]').val()

	reset_helper_texts()
	if(!hasContent(newPassword.trim())){
		$('input[name=new_password]').val('')
		change_helper_texts($('input[name=new_password]').parent().siblings('.helper-text'),'Password is required!','#dc3545')
		return false
	}
	if(!hasContent(repeatedPassword.trim())){
		$('input[name=repeat_password]').val('')
		change_helper_texts($('input[name=repeat_password]').parent().siblings('.helper-text'),'Password is required!','#dc3545')
		return false
	}

	if(!(/[a-z]/).test(newPassword)||!(/[0-9]/).test(newPassword)||!(/[A-Z]/).test(newPassword)){
		change_helper_texts($('input[name=new_password]').parent().siblings('.helper-text'),'Password must contain an uppercase, lowercase and numeric character!','#dc3545')
		return false
	}

	if(newPassword.length<8){
		change_helper_texts($('input[name=new_password]').parent().siblings('.helper-text'),'Password must be atleast 8 characters long!','#dc3545')
		return false
	}

	if(newPassword.localeCompare(repeatedPassword)!==0){
		change_helper_texts($('input[name=repeat_password]').parent().siblings('.helper-text'),'Passwords do NOT Match!','#dc3545')
		return false
	}

	$(form.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Changing . . .')
	$.ajax({
		url: $(form.currentTarget).attr('action'),
		data: {
			new_password: newPassword,
			repeat_password: repeatedPassword
		},
		dataType: 'json',
		method: 'POST'
	}).then(response=>{
		if(!response.ok){
			$('#change-password-button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Login')
			$('#page-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			return
		}
		window.location.assign(`${base_url}`)
	})
}

function registerOwnerLogic(form){
	form.preventDefault()
	reset_helper_texts()
	var fname = $(form.currentTarget).find('input[name=fname]').val().trim(),
	lname = $(form.currentTarget).find('input[name=lname]').val().trim(),
	company = $(form.currentTarget).find('input[name=company]').val().trim(),
	formData = [
		{
			data: fname,
			helperText: $(form.currentTarget).find('input[name=fname]').parent().siblings('.helper-text')
		},
		{
			data: lname,
			helperText: $(form.currentTarget).find('input[name=lname]').parent().siblings('.helper-text')
		},
		{
			data: company,
			helperText: $(form.currentTarget).find('input[name=company]').parent().siblings('.helper-text')
		}
	]
	for(data in formData){
		if(!hasContent(formData[data].data)){
			change_helper_texts(formData[data].helperText,'This is Required!','#dc3545')
			return false
		}
		if(/[^a-z \'-]/i.test(formData[data].data)){
			change_helper_texts(formData[data].helperText,'This Field Contains Invalid Characters!','#dc3545')
			return false
		}
	}

	$(form.currentTarget).find('button').attr('disabled',true).removeClass('width-100').addClass('disabled').html('<span class="spinner-border spinner-border-sm"></span> Finishing Up . . .')

	$.ajax({
		url: $(form.currentTarget).attr('action'),
		data: {
			fname,
			lname,
			company
		},
		dataType: 'json',
		method: 'POST'
	}).then(response=>{
		if(!response.ok){
			$('#finish-button').attr('disabled',false).removeClass('disabled').addClass('width-100').html('Login')
			$('#page-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			return
		}
		window.location.reload(true)
	})
}

function test_email(email){
	var $find1 = email.indexOf('@');
   	var $find2 = email.lastIndexOf('.');
    return ($find1 !== -1 && $find2 !== -1 && ($find1+2)<$find2 && ($find2+2)<email.length);
}

function hasContent($var){
	return $var && $var.length>0;
}

function reset_helper_texts(){
	var helper_texts = $('.helper-text');
	for(var i=0; i<helper_texts.length; i++){
		$(helper_texts[i]).text($(helper_texts[i]).data('original')).css({color: 'rgba(0,0,0,0.54)'}).parent().find('input,select').css({borderColor: '#ced4da'})
	}
}

function change_helper_texts(span,text,color){
	$(span).text(text).css({color: color}).parent().find('input,select').css({borderColor: color})
}

function capitalize(word){
	if(!word) return ''
	var array = word.trim().split(' ')
	array.forEach((element,index)=>{
		array[index] = array[index].charAt(0).toUpperCase() + array[index].slice(1).toLowerCase()
	})
	return array.join(' ')
}

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

function getLocationDetails(selector = '.set-country-name-here'){
	return $.ajax('https://ipapi.co/json').then(data => {
		$(selector).val(data.country_name.toLowerCase())
		$(selector).trigger('change')
	})
}

function formatNumberCurrency(number, currencyFormat){
	try{
		return new Intl.NumberFormat(navigator.language, { style: 'currency', currency: currencyFormat}).format(number)
	}catch(ex){
		return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD'}).format(number)
	}
}

function randomizeColor(){
	var randoms = $('.bg-random')
	for (var i = 0; i < randoms.length; i++) {
		$(randoms[i]).css({backgroundColor: randomColorArray[Math.floor(Math.random() * 10)]})
	}
}

function bounce(target){
	anime({targets: target,scale: {value: 0.7,duration: 50}}).finished.then(()=>{
		anime({targets: target,scale: {value: 1, duration: 700}})
	})
}


$(()=>{
	var forms = $('form')
	for(var i=0; i<forms.length; i++){
		if($(forms[i]).hasClass('fade')) $(forms[i]).hide().removeClass('fade').fadeIn(850)
	}
	reset_helper_texts()
	randomizeColor()
	$('.bg-random-persistent').css({backgroundColor: bgRandom})
	let inputs = $('input').not('.gn-search').not('[readonly]')
	if(inputs.length > 0){
		inputs[0].focus()
	}
	$('.modal').on('shown.bs.modal', event => {
		let modal = $(event.currentTarget).find('input').not('[readonly]')
		if(modal.length > 0){
			modal[0].focus()
		}
	})
	getLocationDetails()
})
angular.module('main', ['datatables']).directive('viewProducts', ['productData', '$timeout', (productData, $timeout) => {
	return {
		restrict: 'E',
		scope: {
			productsURL: '@url',
			transactionType: '@type'
		},
		link: function (scope, element, attrs) {
			scope.allProducts = []
			scope.products = []
			scope.fetchingProducts = true
			scope.noProducts = true
			scope.searchProducts = ''
			scope.isSale = (scope.transactionType.localeCompare('sale') === 0)
			scope.isPurchase = (scope.transactionType.localeCompare('purchase') === 0)
			scope.formatCurrency = scope.$parent.formatCurrency

           	scope.fetchProducts = async function(){
           		let products = await productData.fetchValidProducts(scope.productsURL)
           		scope.allProducts = products
           		scope.noProducts = (scope.allProducts.length < 1)
           		scope.fetchingProducts = false
           		scope.$apply()
           	}
	        $timeout(() =>{
				scope.fetchProducts()
			}, 1000)
           	scope.roundOff = function($var, $dp){
           		try{
           			return parseFloat($var).toFixed($dp)
           		}catch(err){
           			return parseFloat(0).toFixed($dp)
           		}
           	}
           	scope.preAddToCart = function(product){
           		scope.$parent.inputFields.item1 = product
           		scope.$parent.inputFields.item2 = ''
           		scope.$parent.inputFields.item3 = ''
           		scope.$parent.inputFields.item4 = ''
           		scope.$parent.updateCart = false
           	}
           	scope.addSalesToCart = function(product){
           		let found = false
           		for (var i = 0; i < scope.$parent.cartItems.length; i++) {
           			if(scope.$parent.cartItems[i].item1.product_id.localeCompare(product.product_id) === 0){
           				scope.$parent.cartItems[i].item2 = String(parseInt(scope.$parent.cartItems[i].item2) + 1)
           				scope.$parent.cartItems[i].item3 = String(parseInt(scope.$parent.cartItems[i].item2) * parseInt(product.cost_per_unit))
           				bounce('#cart-items')
           				found = true
           				break;
           			}
           		}
           		if(!found){
           			let cost = product.cost_per_unit
	           		scope.$parent.cartItems.push({item1: product, item2: '1', item3: cost, item4: '0'})
           		}
           		scope.$parent.inputFields.item1 = {}
           		scope.$parent.inputFields.item2 = ''
           		scope.$parent.inputFields.item3 = ''
           		scope.$parent.inputFields.item4 = ''
           		bounce('#cart-items')
           		return
           	}
           	scope.$watchGroup(['allProducts', 'searchProducts'], () => {
           		scope.products = scope.allProducts.filter((item) => {
       				item.product = capitalize(item.product)
           			item.category_name = capitalize(item.category_name)
           			return !scope.searchProducts || scope.searchProducts.length < 1 || 
           			item.product.toLowerCase().indexOf(scope.searchProducts.toLowerCase()) !== -1 || 
           			scope.searchProducts.toLowerCase().indexOf(item.product.toLowerCase()) !== -1 || 
           			item.category_name.toLowerCase().indexOf(scope.searchProducts.toLowerCase()) !== -1 || 
           			scope.searchProducts.toLowerCase().indexOf(item.category_name.toLowerCase()) !== -1
           		})
           		scope.noProducts = (scope.products.length < 1 && !scope.fetchingProducts)
           		$timeout(()=>{
           			randomizeColor()
           		})
           	})
           	scope.$watch('$parent.formatCurrency',() => {
				scope.formatCurrency = scope.$parent.formatCurrency
			})
			scope.$watch('$parent.reloadProducts',(newValue) => {
				if(newValue){
					$timeout(() =>{
						scope.fetchProducts()
					}, 1000)
					scope.$parent.reloadProducts = false
				}
			})
        },
		template: viewProductsTemplate
	}
}]).directive('viewCart', [() => {
	return {
		restrict: 'E',
		scope: {
			transactionType: '@type'
		},
		link: function (scope, element, attrs) {
			scope.tableInstance = {}

			scope.products = scope.$parent.cartItems
			scope.formatCurrency = scope.$parent.formatCurrency
			scope.inputFields = scope.$parent.inputFields
			scope.checkoutFields = scope.$parent.checkoutFields
			scope.isSale = (scope.transactionType.localeCompare('sale') === 0)
			scope.isPurchase = (scope.transactionType.localeCompare('purchase') === 0)

			scope.preAddToCart = function(product){
           		scope.inputFields.item1 = product.item1
           		scope.inputFields.item2 = product.item2
           		scope.inputFields.item3 = product.item3
           		scope.inputFields.item4 = product.item4
           		scope.$parent.updateCart = true
			}
			scope.removeFromCart = function($index){
				scope.products.splice($index,1)
				bounce('#cart-items')
			}
			scope.getTotalCostAfterDiscount = function(){
				if(scope.products.length<1) return formatNumberCurrency('0', scope.$parent.currencyCode)
				return formatNumberCurrency(scope.products.map((element)=>element.item3).reduce((total,current)=> total = parseInt(total) + parseInt(current) ), scope.$parent.currencyCode)
			}
			scope.getTotalDiscount = function(){
				if(scope.products.length<1) return formatNumberCurrency('0', scope.$parent.currencyCode)
				return formatNumberCurrency(scope.products.map((element)=>element.item4).reduce((total,current)=> total = parseInt(total) + parseInt(current) ), scope.$parent.currencyCode)
			}
			scope.getTotalCostBeforeDiscount = function(){
				if(scope.products.length<1) return formatNumberCurrency('0', scope.$parent.currencyCode)
				return formatNumberCurrency(scope.products.map((element)=> parseInt(element.item4) + parseInt(element.item3) ).reduce((total,current)=> total = parseInt(total) + parseInt(current) ), scope.$parent.currencyCode)
			}
			scope.checkout = function(){
				let totalCost = scope.products.length < 1 ? '0' :  scope.products.map((element)=>element.item3).reduce((total,current)=> total = parseInt(total) + parseInt(current) );
				scope.checkoutFields.item1 = String(totalCost);
				if(!scope.isSale && scope.isPurchase)
					scope.checkoutFields.item2 = String(totalCost);
			}
			scope.$watch('$parent.cartItems + $parent.formatCurrency + $parent.inputFields + $parent.checkoutFields',() => {
				scope.products = scope.$parent.cartItems
				scope.formatCurrency = scope.$parent.formatCurrency
				scope.inputFields = scope.$parent.inputFields
				scope.checkoutFields = scope.$parent.checkoutFields
			})
        },
		template: viewCartTemplate
	}
}]).factory('productData', () => {
	return {
		fetchValidProducts: function (url){
			return $.ajax({
				url: url,
				dataType: 'json' 
			})
		}
	}
})