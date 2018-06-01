function CheckConfirmation(WrongText, RightText) {
	New = document.getElementById('NewPassword');
	Confirm = document.getElementById('ConfirmPassword');
	if (Confirm.value == New.value) {
		document.getElementById("ConfirmHint").style.backgroundColor='#90EE90';
		document.getElementById("ConfirmHint").style.color='#008000';
		document.getElementById("ConfirmHint").innerHTML=RightText;
	} else {
		document.getElementById("ConfirmHint").style.backgroundColor='#FFC0CB';
		document.getElementById("ConfirmHint").innerHTML=WrongText;
	}
}