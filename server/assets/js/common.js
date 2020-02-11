const validToastTypes = ['success', 'danger', 'info', 'warning'];
const serverTimeZone = '180';

function viewPassword(span, input) {
	let $input = $(input).attr('type');
	if ($input.localeCompare('password') === 0) {
		$(input).attr('type', 'text');
		$(span).find("i").removeClass('fa-eye').addClass('fa-eye-slash');
	} else {
		$(input).attr('type', 'password');
		$(span).find("i").removeClass('fa-eye-slash').addClass('fa-eye');
	}
	$(input).focus();
	return false
}

function setToast(message, type) {
	type = type.toLowerCase().trim();
	if (validToastTypes.indexOf(type) === -1) return false;
	const element = $("#site-info>div.toast");
	const html = `<div class='toast-body alert alert-${type} mb-0'>${message}</div>`;
	return element.html(html).toast('show');
}

function setPageErrors(element, message) {
	return $(element)
		.html(`<div><i class='fas fa-exclamation-circle'><i><strong> Errors: </strong>${message}</div>`)
		.parent()
		.toast({delay: 5000})
		.toast('show')
}

function toggleButton(button, html) {
	const disabled = !$(button).attr('disabled');
	const removeClass = disabled ? 'width-100' : 'disabled';
	const addClass = disabled ? 'disabled' : 'width-100';
	$(button)
		.attr({'disabled': disabled})
		.removeClass(removeClass)
		.addClass(addClass)
		.html(html);
}

function getTimezoneEquivalentDate(date) {
	return moment(date).subtract(serverTimeZone, 'minutes').add(moment().utcOffset(), 'minutes').format();
}
