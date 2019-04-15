
function loginLogic(form){
	form.preventDefault()

	var username = $(form.currentTarget).find('input[name=username]').val().trim(),
	password = $(form.currentTarget).find('input[name=password]').val()

	if(!validate_login_details(username,password)) return

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
			$('#login-errors>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>"+response.errors+"</div>").parent().toast({delay:5000}).toast('show')
			return
		}
		window.location.reload(true)
	})
}

function validate_login_details(username,password){
	reset_helper_texts()
	if(!hasContent(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Username is required!','#dc3545')
		return false
	}
	if(/[^a-z0-9 ]/.test(username)){
		change_helper_texts($('input[name=username]').parent().siblings('.helper-text'),'Username contains invalid characters!','#dc3545')
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

function hasContent($var){
	return $var && $var.length>0;
}

function reset_helper_texts(){
	var helper_texts = $('.helper-text');
	for(var i=0; i<helper_texts.length; i++){
		$(helper_texts[i]).text($(helper_texts[i]).data('original')).css({color: 'rgba(0,0,0,0.54)'}).parent().find('input').css({borderColor: '#ced4da'})
	}
}

function change_helper_texts(span,text,color){
	$(span).text(text).css({color: color}).parent().find('input').css({borderColor: color})
}

$(()=>{
	$('form').fadeIn(800)
	reset_helper_texts()
	$('input')[0].focus()
})