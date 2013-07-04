$(document).ready(function() {
	$('#signupOpenId_firstName').bind("change blur keyup mouseup", function() {
		$('#signupUserPass_firstName').val($('#signupOpenId_firstName').val());
	});
	$('#signupUserPass_firstName').bind("change blur keyup mouseup", function(){
		$('#signupOpenId_firstName').val($('#signupUserPass_firstName').val());
	});
	$('#signupOpenId_lastName').bind("change blur keyup mouseup", function() {
		$('#signupUserPass_lastName').val($('#signupOpenId_lastName').val());
	});
	$('#signupUserPass_lastName').bind("change blur keyup mouseup", function(){
		$('#signupOpenId_lastName').val($('#signupUserPass_lastName').val());
	});
});
