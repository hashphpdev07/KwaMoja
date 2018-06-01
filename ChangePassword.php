<?php
include ('includes/session.php');

$Title = _('Change User Password');

include ('includes/header.php');
echo '<script type="text/javascript" defer="defer" src = "', $RootPath, '/javascripts/ChangePassword.js"></script>';
/* Assume all is well */
$InputError = 0;

if (isset($_POST['Submit'])) {
	$SQL = "SELECT password FROM www_users WHERE userid='" . $_SESSION['UserID'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	$CurrentPassword = $MyRow['password'];

	/* Is the old password correct */
	if (!VerifyPass($_POST['OldPassword'], $CurrentPassword)) {
		$InputError = 1;
		prnMsg(_('You have entered the wrong current password, please re-enter'), 'error');
	}

	/* Is the new password different to the old one */
	if ($_POST['OldPassword'] == $_POST['NewPassword']) {
		$InputError = 1;
		prnMsg(_('The new password is the same as the old. You must choose a new password.'), 'error');
	}

	if ($_POST['NewPassword'] != $_POST['ConfirmPassword']) {
		$InputError = 1;
		prnMsg(_('The confirmation and the new passwords are not the same.'), 'error');
	}

	if ($InputError == 0) {
		if ($AllowDemoMode == True) {
			prnMsg(_('You are running in demonstration mode so the password cannot be changed. Click') . '&nbsp;<a href="Logout.php">' . _('here') . '&nbsp' . _('to logout and Login again with your current password. Your password has not been changed'), 'info');
			$ResetSQL = "UPDATE www_users SET changepassword=0 WHERE userid='" . $_SESSION['UserID'] . "'";
			$Result = DB_query($ResetSQL);
		} else {
			$UpdateSQL = "UPDATE www_users SET changepassword=0, password='" . CryptPass($_POST['NewPassword']) . "' WHERE userid='" . $_SESSION['UserID'] . "'";
			$Result = DB_query($UpdateSQL);
			if (DB_error_no($Result) == 0) {
				prnMsg(_('Your password has benn updated. Click') . '&nbsp;<a href="Logout.php">' . _('here') . '&nbsp' . _('to logout and Login again with your new password.'), 'success');
			}
		}
	}
}

if ($InputError == 1 or !isset($_POST['Submit'])) {
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Change Password'), '" alt="" />', _('Change your Password'), '
		</p>';

	prnMsg(_('The admin has blocked your account until you change your password. Use this screen to change your password before continuing.'), 'info');

	if (!isset($_POST['OldPassword'])) {
		$_POST['OldPassword'] = '';
	}

	if (!isset($_POST['NewPassword'])) {
		$_POST['NewPassword'] = '';
	}

	if (!isset($_POST['ConfirmPassword'])) {
		$_POST['ConfirmPassword'] = '';
	}

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	echo '<fieldset>
			<legend>', _('You must change your password'), '</legend>
			<field>
				<label for="OldPassword">', _('Current Password'), '</label>
				<input type="password" name="OldPassword" required="required" autofocus="autofocus" value="', $_POST['OldPassword'], '" />
				<fieldhelp>', _('Enter your current password here'), '</fieldhelp>
			</field>
			<field>
				<label for="NewPassword">', _('New Password'), '</label>
				<input type="password" id="NewPassword" name="NewPassword" required="required" value="', $_POST['NewPassword'], '" />
				<fieldhelp>', _('Enter your new password here'), '</fieldhelp>
			</field>
			<field>
				<label for="ConfirmPassword">', _('Confirm the new Password'), '</label>
				<input type="password" id="ConfirmPassword" name="ConfirmPassword" required="required" value="', $_POST['ConfirmPassword'], '" onkeyup="CheckConfirmation(\'', _('The confirmation does not agree with the new password'), '\',\'', _('The new password and the confirmation agree'), '\')" />
				<fieldhelp id="ConfirmHint">', _('Your new password does not agree with the confirmation'), '</fieldhelp>
			</field>
		</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="Submit" value="Submit" />
		</div>';

	echo '</form>';
}

include ('includes/footer.php');

?>