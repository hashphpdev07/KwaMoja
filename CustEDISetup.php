<?php
include ('includes/session.php');
$Title = _('Customer EDI Set Up');
include ('includes/header.php');

echo '<a href="' . $RootPath . '/SelectCustomer.php">' . _('Back to Customers') . '</a><br />';

echo '<p class="page_title_text" ><img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/customer.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (ContainsIllegalCharacters($_POST['EDIReference']) or mb_strstr($_POST['EDIReference'], ' ')) {
		$InputError = 1;
		prnMsg(_('The customers EDI reference code cannot contain any of the following characters') . ' - \' &amp; + \" ' . _('or a space'), 'warn');
	}
	if (mb_strlen($_POST['EDIReference']) < 4 and ($_POST['EDIInvoices'] == 1 or $_POST['EDIOrders'] == 1)) {
		$InputError = 1;
		prnMsg(_('The customers EDI reference code must be set when EDI Invoices or EDI orders are activated'), 'warn');
	}
	if (mb_strlen($_POST['EDIAddress']) < 4 and $_POST['EDIInvoices'] == 1) {
		$InputError = 1;
		prnMsg(_('The customers EDI email address or FTP server address must be entered if EDI Invoices are to be sent'), 'warn');
	}

	if ($InputError == 0) { //ie no input errors
		if (!isset($_POST['EDIServerUser'])) {
			$_POST['EDIServerUser'] = '';
		}
		if (!isset($_POST['EDIServerPwd'])) {
			$_POST['EDIServerPwd'] = '';
		}
		$SQL = "UPDATE debtorsmaster SET ediinvoices ='" . $_POST['EDIInvoices'] . "',
					ediorders ='" . $_POST['EDIOrders'] . "',
					edireference='" . $_POST['EDIReference'] . "',
					editransport='" . $_POST['EDITransport'] . "',
					ediaddress='" . $_POST['EDIAddress'] . "',
					ediserveruser='" . $_POST['EDIServerUser'] . "',
					ediserverpwd='" . $_POST['EDIServerPwd'] . "'
			WHERE debtorno = '" . $_SESSION['CustomerID'] . "'";

		$ErrMsg = _('The customer EDI setup data could not be updated because');
		$Result = DB_query($SQL, $ErrMsg);
		prnMsg(_('Customer EDI configuration updated'), 'success');
	} else {
		prnMsg(_('Customer EDI configuration failed'), 'error');
	}
}

echo '<form method="post" action="' . htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table>';

$SQL = "SELECT debtorno,
		name,
		ediinvoices,
		ediorders,
		edireference,
		editransport,
		ediaddress,
		ediserveruser,
		ediserverpwd
	FROM debtorsmaster
	WHERE debtorno = '" . $_SESSION['CustomerID'] . "'";

$ErrMsg = _('The customer EDI configuration details could not be retrieved because');
$Result = DB_query($SQL, $ErrMsg);

$MyRow = DB_fetch_array($Result);

echo '<tr>
		<td>' . _('Customer Code') . ':</td>
		<td>' . $_SESSION['CustomerID'] . '</td>
	</tr>';
echo '<tr>
		<td>' . _('Customer Name') . ':</td>
		<td>' . $MyRow['name'] . '</td>
	</tr>';
echo '<tr>
		<td>' . _('Enable Sending of EDI Invoices') . ':</td>
		<td><select name="EDIInvoices">';

if ($MyRow['ediinvoices'] == 0) {

	echo '<option selected="selected" value="0">' . _('Disabled') . '</option>';
	echo '<option value="1">' . _('Enabled') . '</option>';
} else {
	echo '<option value="0">' . _('Disabled') . '</option>';
	echo '<option selected="selected" value="1">' . _('Enabled') . '</option>';
}

echo '</select><a href="' . $RootPath . '/EDIMessageFormat.php?MessageType=INVOIC&amp;PartnerCode=' . urlencode($_SESSION['CustomerID']) . '">' . _('Create') . '/' . _('Edit Invoice Message Format') . '</a></td>
	</tr>';

echo '<tr>
		<td>' . _('Enable Receiving of EDI Orders') . ':</td>
		<td><select name="EDIOrders">';

if ($MyRow['ediorders'] == 0) {

	echo '<option selected="selected" value="0">' . _('Disabled') . '</option>';
	echo '<option value="1">' . _('Enabled') . '</option>';
} else {
	echo '<option value="0">' . _('Disabled') . '</option>';
	echo '<option selected="selected" value="1">' . _('Enabled') . '</option>';
}

echo '</select></td>
	</tr>';

echo '<tr>
		<td>' . _('Customer EDI Reference') . ':</td>
		<td><input type="text" name="EDIReference" size="20" required="required" maxlength="20" value="' . $MyRow['edireference'] . '" /></td>
	</tr>';

echo '<tr>
		<td>' . _('EDI Communication Method') . ':</td>
		<td><select name="EDITransport" >';

if ($MyRow['editransport'] == 'email') {
	echo '<option selected="selected" value="email">' . _('Email Attachments') . '</option>';
	echo '<option value="ftp">' . _('File Transfer Protocol (FTP)') . '</option>';
} else {
	echo '<option value="email">' . _('Email Attachments') . '</option>';
	echo '<option selected="selected" value="ftp">' . _('File Transfer Protocol (FTP)') . '</option>';
}

echo '</select></td></tr>';

echo '<tr>
		<td>' . _('FTP Server or Email Address') . ':</td>
		<td><input type="text" name="EDIAddress" size="42" required="required" maxlength="40" value="' . $MyRow['ediaddress'] . '" /></td>
	</tr>';

if ($MyRow['editransport'] == 'ftp') {

	echo '<tr><td>' . _('FTP Server User Name') . ':</td>
			<td><input type="text" name="EDIServerUser" size="20" required="required" maxlength="20" value="' . $MyRow['ediserveruser'] . '" /></td></tr>';
	echo '<tr><td>' . _('FTP Server Password') . ':</td>
			<td><input type="text" name="EDIServerPwd" size="20" required="required" maxlength="20" value="' . $MyRow['ediserverpwd'] . '" /></td></tr>';
}

echo '</table>
		<div class="centre">
			<input type="submit" name="submit" value="' . _('Update EDI Configuration') . '" />
		</div>
	</form>';

include ('includes/footer.php');
?>