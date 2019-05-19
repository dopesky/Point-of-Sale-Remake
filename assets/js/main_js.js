
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
	var array = word.split(' ')
	array.forEach((element,index)=>{
		array[index] = array[index].charAt(0).toUpperCase() + array[index].slice(1)
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


$(()=>{
	var forms = $('form')
	for(var i=0; i<forms.length; i++){
		if($(forms[i]).hasClass('fade')) $(forms[i]).hide().removeClass('fade').fadeIn(850)
	}
	reset_helper_texts()
	$('input')[0].focus()
})
angular.module('main', ['datatables'])