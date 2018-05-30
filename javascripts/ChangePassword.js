function CheckConfirmation() {
	New = document.getElementById('NewPassword');
	Confirm = document.getElementById('ConfirmPassword');
	if (Confirm.value == New.value) {
//		alert('hello');
		document.getElementById("ConfirmHint").style.backgroundColor='#90EE90';
		document.getElementById("ConfirmHint").style.color='#008000';
		document.getElementById("ConfirmHint").innerHTML="The new password and confirmation agree";
	} else {
		document.getElementById("ConfirmHint").style.backgroundColor='#FFC0CB';
	}
}