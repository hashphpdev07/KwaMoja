<?php
// Display demo user name and password within login form if $AllowDemoMode is true
include ($PathPrefix . 'includes/LanguageSetup.php');
include ('LanguagesArray.php');
include ('MobileDetect.php');
$MobileDetect = new Mobile_Detect;
if ((isset($AllowDemoMode)) and ($AllowDemoMode == True) and (!isset($demo_text))) {
	$demo_text = _('Login as user') . ': <i>' . _('admin') . '</i><br />' . _('with password') . ': <i>' . _('kwamoja') . '</i>';
} elseif (!isset($demo_text)) {
	$demo_text = _('Please login here');
}

//echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
echo '<html>
		<head>
			<title>' . $ProjectName . ' ' . _('Login screen') . '</title>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="shortcut icon" href="favicon.ico?v=2" type="image/x-icon" />';

if ($LanguagesArray[$DefaultLanguage]['Direction'] == 'rtl') {
	echo '<link rel="stylesheet" href="css/login_rtl.css" type="text/css" />';
} else {
	//	echo '<link rel="stylesheet" href="css/login-mobile.css" type="text/css" />';
	echo '<link rel="stylesheet" href="css/login.css" type="text/css" />';
}
?>
</head>
<body>

<div id="container">
	<table>
		<tr>
			<th colspan="2">
				<div id="login_logo">
					<a href="<?php echo $HomePage; ?>" target="_blank"><img src="css/<?php echo $DefaultDatabase; ?>.png" style="width:100%" /></a>
				</div>
			</th>
		</tr>
		<tr>
			<td id="login-container">
				<div id="login_box">
					<form action="index.php" name="LogIn" method="post" class="noPrint">
					<input type="hidden" name="FormID" value="<?php
echo $_SESSION['FormID'];
?>" />
					<label><?php
echo _('Company');
?>:</label>

					<?php
if (isset($_COOKIE['Login'])) {
	$DefaultCompany = $_COOKIE['Login'];
}
if ($AllowCompanySelectionBox === 'Hide') {
	// do not show input or selection box
	echo '<input type="hidden" name="CompanyNameField"  value="' . $DefaultCompany . '" />';
} else if ($AllowCompanySelectionBox === 'ShowInputBox') {
	// show input box
	echo '<input type="text" required="required" autofocus="autofocus" name="CompanyNameField"  value="' . $DefaultCompany . '" />';
} else {
	// Show selection box ($AllowCompanySelectionBox == 'ShowSelectionBox')
	echo '<select name="CompanyNameField">';

	$DirHandle = dir('companies/');

	while (false !== ($CompanyEntry = $DirHandle->read())) {
		if (is_dir('companies/' . $CompanyEntry) and $CompanyEntry != '..' and $CompanyEntry != '' and $CompanyEntry != '.' and $CompanyEntry != 'default') {
			if (file_exists('companies/' . $CompanyEntry . '/Companies.php')) {
				include ('companies/' . $CompanyEntry . '/Companies.php');
			} else {
				$CompanyName[$CompanyEntry] = $CompanyEntry;
			}
			if ($CompanyEntry == $DefaultCompany) {
				echo '<option selected="selected" value="' . $CompanyEntry . '">' . $CompanyName[$CompanyEntry] . '</option>';
			} else {
				echo '<option value="' . $CompanyEntry . '">' . $CompanyName[$CompanyEntry] . '</option>';
			}
		}
	}

	$DirHandle->close();

	echo '</select>';
}
?>

					<br />
					<label><?php
echo _('User name');
?>:</label>
					<input type="text" autofocus="autofocus" required="required" name="UserNameEntryField" placeholder="<?php echo _('User name'); ?>" maxlength="20" /><br />
					<label><?php
echo _('Password');
?>:</label>
					<input type="password" required="required" name="Password" placeholder="<?php echo _('Password'); ?>" />
	   <div id="demo_text">
	   <?php
if (isset($demo_text)) {
	echo $demo_text;
}
?>
	   </div>
					<button class="button" type="submit" value="<?php
echo _('Login');
?>" name="SubmitUser">
					<?php
echo _('Login');
?>
					 <img src="css/tick.png" title="' . _('Upgrade') . '" alt="" class="ButtonIcon" /></button>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>

</body>
</html>