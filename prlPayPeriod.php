<?php
include ('includes/session.php');

$Title = _('Pay Period Section');

include ('includes/header.php');

if (isset($_GET['PayPeriodID'])) {
	$PayPeriodID = $_GET['PayPeriodID'];
} elseif (isset($_POST['PayPeriodID'])) {
	$PayPeriodID = $_POST['PayPeriodID'];
} else {
	unset($PayPeriodID);
}

echo '<p class="page_title_text noPrint">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

if (isset($_POST['insert']) or isset($_POST['update'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['PayPeriodName'], '&') > 0 or strpos($_POST['PayPeriodName'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The Pay Period description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['PayPeriodName']) == '') {
		$InputError = 1;
		prnMsg(_('The Pay Period description may not be empty'), 'error');
	}
	if (is_numeric($_POST['PayPeriodName'])) /* Check if the bank code is numeric */ {
		prnMsg(_('Pay Description must be Character'), 'error');
		$InputError = 1;
	}
	if (strlen($PayPeriodID) == 0) {
		$InputError = 1;
		prnMsg(_('The Pay Period Code cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (isset($_POST['update'])) {

			$SQL = "UPDATE prlpayperiod SET payperioddesc='" . $_POST['PayPeriodName'] . "',
							numberofpayday='" . $_POST['NumberOfPayday'] . "',
							dayofpay='" . $_POST['DayOfPay'] . "'
						WHERE payperiodid = '$PayPeriodID'";

			$ErrMsg = _('The pay period could not be updated because');
			$DbgMsg = _('The SQL that was used to update the pay period but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The pay period master record for') . ' ' . $PayPeriodID . ' ' . _('has been updated'), 'success');
			unset($PayPeriodID);
			unset($_POST['PayPeriodName']);
			unset($_POST['NumberOfPayday']);

		} elseif (isset($_POST['insert'])) { //its a new pay period
			$SQL = "INSERT INTO prlpayperiod (payperiodid,
							payperioddesc,
							numberofpayday,
							dayofpay
						) VALUES (
							'" . $PayPeriodID . "',
							'" . $_POST['PayPeriodName'] . "',
							'" . $_POST['NumberOfPayday'] . "',
							'" . $_POST['DayOfPay'] . "'
						)";

			$ErrMsg = _('The pay period') . ' ' . $_POST['PayPeriodName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the pay period but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new pay period for') . ' ' . $_POST['PayPeriodName'] . ' ' . _('has been added to the database'), 'success');

			unset($PayPeriodID);
			unset($_POST['PayPeriodName']);
			unset($_POST['NumberOfPayday']);
			unset($_POST['DayOfPay']);
		}

	} else {
		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');
	}

} elseif (isset($_GET['delete']) and $_GET['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlpayperiod WHERE payperiodid='$PayPeriodID'";
		$Result = DB_query($SQL);
		prnMsg(_('Pay Period record for') . ' ' . $PayPeriodID . ' ' . _('has been deleted'), 'success');
		unset($PayPeriodID);
		unset($_SESSION['PayPeriodID']);
	} //end if Delete paypayperiod
	
}

$SQL = "SELECT payperiodid,
				payperioddesc,
				numberofpayday,
				dayofpay
			FROM prlpayperiod
			ORDER BY payperiodid";

$ErrMsg = _('Could not get pay period because');
$Result = DB_query($SQL, $ErrMsg);

if (DB_num_rows($Result) > 0) {
	echo '<table>
			<tr>
				<th>', _('Pay Code'), '</th>
				<th>', _('Pay Description'), '</th>
				<th>', _('Number of Payday'), '</th>
				<th>', _('Day in Peiod to Pay'), '</th>
				<th colspan="2"></th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr class="striped_row">
				<td>', $MyRow['payperiodid'], '</td>
				<td>', $MyRow['payperioddesc'], '</td>
				<td class="number">', $MyRow['numberofpayday'], '</td>
				<td class="number">', $MyRow['dayofpay'], '</td>
				<td><a href="', basename(__FILE__), '?PayPeriodID=', urlencode($MyRow['payperiodid']), '">', _('Edit'), '</a></td>
				<td><a href="', basename(__FILE__), '?PayPeriodID=', urlencode($MyRow['payperiodid']), '&delete=1">', _('Delete'), '</a></td
			</tr>';
	}
	echo '</table>';
}
//PayPeriodID exists - either passed when calling the form or from the form itself
echo '<form method="post" class="noPrint" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

//if (!isset($_POST["New"])) {
if (isset($PayPeriodID)) {
	$SQL = "SELECT payperiodid,
				payperioddesc,
				numberofpayday
			FROM prlpayperiod
			WHERE payperiodid = '" . $PayPeriodID . "'";

	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);

	$_POST['PayPeriodName'] = $MyRow['payperioddesc'];
	$_POST['NumberOfPayday'] = $MyRow['numberofpayday'];
	echo '<input type="hidden" name="PayPeriodID" value="' . $PayPeriodID . '">';
	echo '<fieldset>
			<legend>', _('Edit payroll period'), '</legend>
			<field>
				<label for="PayPeriodID">', _('Pay Period Code'), ':</label>
				<div class="fieldtext">', $PayPeriodID, '</div>
			</field>';
} else {
	// its a new supplier being added
	echo '<fieldset>
			<legend>', _('Create payroll period'), '</legend>
			<field>
				<label for="PayPeriodID">', _('Pay Period Code'), ':</label>
				<input type="text" class="number" name="PayPeriodID" value="" autofocus="autofocus" required="required" size="5" maxlength="4" />
				<fieldhelp>', _('The ID by which this pay period will be known. This must be an integer.'), '</fieldhelp>
			</field>';
	$_POST['PayPeriodName'] = '';
	$_POST['NumberOfPayday'] = 0;
}
echo '<field>
		<label for="PayPeriodName">', _('Pay Description'), ':</label>
		<input type="text" name="PayPeriodName" size="16" maxlength="15" value="', $_POST['PayPeriodName'], '" />
		<fieldhelp>', _('A short description of this pay period.'), '</fieldhelp>
	</field>';
echo '<field>
		<label for="NumberOfPayday">', _('Number of Pay Day'), ':</label>
		<input type="text" class="number" name="NumberOfPayday" size="12" maxlength="11" value="', $_POST['NumberOfPayday'], '" />
	</field>';
echo '<field>
		<label for="DayOfPay">', _('Day in Period to Pay'), ':</label>
		<input type="text" class="number" name="DayOfPay" size="12" maxlength="11" value="', $_POST['NumberOfPayday'], '" />
	</field>
</fieldset>';

if (!isset($PayPeriodID)) {
	echo '<div class="centre">
			<input type="submit" name="insert" value="', _('Add These New Pay Period Details'), '" />
		</div>
	</form>';
} else {
	echo '<div class="centre">
			<input type="submit" name="update" value="', _('Update Pay Period'), '">
		</div>
	</form>';
}

include ('includes/footer.php');
?>