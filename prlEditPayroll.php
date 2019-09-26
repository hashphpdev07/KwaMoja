<?php
/* $Revision: 1.0 $ */

$PageSecurity = 5;

include ('includes/session.php');

$Title = _('Payroll Records Maintenance');

include ('includes/header.php');
include ('includes/prlFunctions.php');

if (isset($_GET['PayrollID'])) {
	$PayrollID = $_GET['PayrollID'];
} elseif (isset($_POST['PayrollID'])) {
	$PayrollID = $_POST['PayrollID'];
} else {
	unset($PayrollID);
}

if (isset($_POST['submit'])) {
	//initialise no input errors assumed initially before we test
	$InputError = 0;
	// Checking if Employee ID is set
	if ($PayrollID == "") {
		$InputError = 1;
		prnMsg(_('Payroll ID must not be empty.'), 'error');
	}
	if ($_POST['PayPeriodID'] == "") {
		$InputError = 1;
		prnMsg(_('PayPeriod ID must not be empty.'), 'error');
	}
	if (!is_date($_POST['StartDate'])) {
		$InputError = 1;
		prnMsg(_('The field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'], 'error');
	}
	if (!is_date($_POST['EndDate'])) {
		$InputError = 1;
		prnMsg(_('The field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'], 'error');
	}

	if ($_POST['submit'] != _('Update Payroll Period')) {
		if (($_POST[FSMonth] == "") or ($_POST[FSYear] == "")) {
			echo "<ul><li>FS Month is a Mandatory Field.</li></ul>";
			$InputError = 1;
		}
	}

	if ($InputError != 1) {

		$SQL_StartDate = FormatDateForSQL($_POST['StartDate']);
		$SQL_EndDate = FormatDateForSQL($_POST['EndDate']);

		if (!isset($_POST['New'])) {
			$SQL = "UPDATE prlpayrollperiod SET
					payrolldesc='" . DB_escape_string($_POST['Description']) . "',
					payperiodid='" . DB_escape_string($_POST['PayPeriodID']) . "',
					startdate='" . $SQL_StartDate . "',
					enddate='" . $SQL_EndDate . "',
					fsmonth='" . $_POST['FSMonth'] . "',
					fsyear='" . $_POST['FSYear'] . "',
					deductsss='" . DB_escape_string($_POST['SSS']) . "',
					deducthdmf='" . DB_escape_string($_POST['HDMF']) . "',
					deductphilhealth='" . DB_escape_string($_POST['PhilHealth']) . "'
					 WHERE payrollid = '$PayrollID'";
			$ErrMsg = _('The payroll record could not be updated because');
			$DbgMsg = _('The SQL that was used to update the payroll failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The payroll master record for') . ' ' . $PayrollID . ' ' . _('has been updated'), 'success');

		} else { //its a new payroll
			$SQL = "INSERT INTO prlpayrollperiod (
					payrollid,
					payrolldesc,
					payperiodid,
					startdate,
					enddate,
					fsmonth,
					fsyear,
					deductsss,
					deducthdmf,
					deductphilhealth,
					payclosed)
				VALUES ('$PayrollID',
					'" . DB_escape_string($_POST['Description']) . "',
					'" . DB_escape_string($_POST['PayPeriodID']) . "',
					'" . $SQL_StartDate . "',
					'" . $SQL_EndDate . "',
					'" . $_POST['FSMonth'] . "',
					'" . $_POST['FSYear'] . "',
					'" . DB_escape_string($_POST['SSS']) . "',
					'" . DB_escape_string($_POST['HDMF']) . "',
					'" . DB_escape_string($_POST['PhilHealth']) . "',
					'0'
					)";
			$ErrMsg = _('The payroll period') . ' ' . $_POST['Description'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the payroll period but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new payroll period for') . ' ' . $_POST['Description'] . ' ' . _('has been added to the database'), 'success');

			unset($PayrollID);
			unset($_POST['Description']);
			unset($_POST['PayPeriodID']);
			unset($SQL_StartDate);
			unset($SQL_EndDate);
			unset($_POST['FSMonth']);
			unset($_POST['FSYear']);
			unset($_POST['SSS']);
			unset($_POST['HDMF']);
			unset($_POST['PhilHealth']);
		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {
	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;
	$SQL = "SELECT counterindex,payrollid,employeeid,basicpay,absent,late,otpay,fsmonth,fsyear
				FROM prlpayrolltrans
				WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
	$PayDetails = DB_query($SQL);
	if (DB_num_rows($PayDetails) > 0) {
		$CancelDelete = 1;
		exit("Payroll can not be deleted. Payroll records found...");
	}

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlpayrollperiod WHERE payrollid='$PayrollID'";
		$Result = DB_query($SQL);
		prnMsg(_('Payroll record for') . ' ' . $Description . ' ' . _('has been deleted'), 'success');
		unset($PayrollID);
		unset($_SESSION['PayrollID']);
	} //end if Delete payroll
	
} //end of (isset($_POST['submit']))


if (!isset($PayrollID)) {
	/*If the page was called without $PayrollID passed to page then assume a new payroll is to be entered*/
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo "<input type='hidden' name='New' value='Yes'>";
	echo '<table>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Payroll ID') . ':</td><td><input type="text" name="PayrollID"  size=11 maxlength=10></td>;
		 <td><align=right><b>Accept Alpha Numeric Character. No spaces in between..</b></td></tr>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Description') . ':</td><td><input type="text" name="Description" size=42 maxlength=40></td></tr>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Pay Period') . ":</td><td><select name='PayPeriodID'>";
	DB_data_seek($Result, 0);
	$SQL = 'SELECT payperiodid, payperioddesc FROM prlpayperiod';
	$Result = DB_query($SQL);
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['PayPeriodID'] == $MyRow['payperiodid']) {
			echo '<option selected="selected" value=' . $MyRow['payperiodid'] . '>' . $MyRow['payperioddesc'];
		} else {
			echo '<option value=' . $MyRow['payperiodid'] . '>' . $MyRow['payperioddesc'];
		}
	} //end while loop
	$DateString = Date($_SESSION['DefaultDateFormat']);
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Start Date') . ' (' . $_SESSION['DefaultDateFormat'] . ':</td><td><input type="text" name="StartDate" value="', $DateString, '" size=12 maxlength=10></td></tr>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('End Date') . ' (' . $_SESSION['DefaultDateFormat'] . ':</td><td><input type="text" name="EndDate" value="', $DateString, '"size=12 maxlength=10></td></tr>';
?>
		   <tr>
				<td width=200 height="20">
				<div align="right"><b>FS Month :</b></div>
				</td>
				<td height="20">
				<select name="FSMonth">
				<option value="" SELECTED>Month</option>
				<option value=01>January</option>
				<option value=02>February</option>
				<option value=03>March</option>
				<option value=04>April</option>
				<option value=05>May</option>
				<option value=06>June</option>
				<option value=07>July</option>
				<option value=08>August</option>
				<option value=09>September</option>
				<option value=10>October</option>
				<option value=11>November</option>
				<option value=12>December</option>
				</select>
				<select name="FSYear">
				<option value="" Selected>Year</option>
				<?

					for ($yy=2006;$yy<=2015;$yy++)
					{
						echo "<option value=$yy>$yy</option>\n";

					}
				?>
				</select>
				<? echo $star; ?>
				</td>
			</tr>
	<?php
	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct SSS') . ":</td><td><select name='SSS'>";
	echo '<option value=0>' . _('Yes');
	echo '<option value=1>' . _('No');
	echo '</select></td></tr>';
	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct HDMF') . ":</td><td><select name='HDMF'>";
	echo '<option value=0>' . _('Yes');
	echo '<option value=1>' . _('No');
	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct PhilHealt') . ":</td><td><select name='PhilHealth'>";
	echo '<option value=0>' . _('Yes');
	echo '<option value=1>' . _('No');
	echo '</select></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New Payroll') . '">';
	echo '</form>';

} else {
	echo "<form method='post' action='" . basename(__FILE__) . '?' . SID . "'>";
	echo '<table>';
	if (!isset($_POST['New'])) {
		$SQL = "SELECT payrollid,
					payrolldesc,
					payperiodid,
					startdate,
					enddate,
					fsmonth,
					fsyear,
					deductsss,
					deducthdmf,
					deductphilhealth,
					payclosed
			FROM prlpayrollperiod
			WHERE payrollid = '$PayrollID'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$_POST['Description'] = $MyRow['payrolldesc'];
		$_POST['PayPeriodID'] = $MyRow['payperiodid'];
		$_POST['StartDate'] = ConvertSQLDate($MyRow['startdate']);
		$_POST['EndDate'] = ConvertSQLDate($MyRow['enddate']);
		$_POST['FSMonth'] = $MyRow['fsmonth'];
		$_POST['FSYear'] = $MyRow['fsyear'];
		$_POST['SSS'] = $MyRow['deductsss'];
		$_POST['HDMF'] = $MyRow['deducthdmf'];
		$_POST['PhilHealth'] = $MyRow['deductphilhealth'];
		$_POST['Status'] = $MyRow['payclosed'];
		echo "<input type=HIDDEN name='PayrollID' value='$PayrollID'>";
	} else {
		// its a new employee  being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Payroll ID') . ':</td><td><input type="text" name="PayrollID" value="', $PayrollID, '" size=12 maxlength=10></td></tr>';
	}
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Description') . ':</td><td><input type="text" name="Description" value="' . $_POST['Description'] . '" size=42 maxlength=40></td></tr>';
	echo '</select></td></tr>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Pay Period') . ':</td><td><select name="PayPeriodID">';
	DB_data_seek($Result, 0);
	$SQL = 'SELECT payperiodid, payperioddesc FROM prlpayperiod';
	$Result = DB_query($SQL);
	while ($MyRow = DB_fetch_array($Result)) {
		if ($MyRow['payperiodid'] == $_POST['PayPeriodID']) {
			echo '<option selected="selected" value=';
		} else {
			echo '<option value=';
		}
		echo $MyRow['payperiodid'] . '>' . $MyRow['payperioddesc'];
	} //end while loop
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('Start Date') . ':</td><td><input type="text" name="StartDate" value="' . $_POST['StartDate'] . '" size=22 maxlength=20></td></tr>';
	echo '<tr><td width=200 height=20><div align="right"><b>' . _('End Date') . ':</td><td><input type="text" name="EndDate" value="' . $_POST['EndDate'] . '" size=22 maxlength=20></td></tr>';
	$MosStr = GetMonthStr($_POST['FSMonth']);
	$Mos = GetMonthStr($_POST['FSMonth']);
	$FSY = $_POST['FSYear'];
	echo '</select></td></tr>';
	echo '<tr><td width=200 height="20"><div align="right"><b>' . _('FS Month') . ":</td><td><select name='FSMonth'>";
	echo '<option selected="selected" value=$Mos>' . _($MosStr);
	echo '<option value=1>' . _('January');
	echo '<option value=2>' . _('February');
	echo '<option value=3>' . _('March');
	echo '<option value=4>' . _('April');
	echo '<option value=5>' . _('May');
	echo '<option value=6>' . _('June');
	echo '<option value=7>' . _('July');
	echo '<option value=8>' . _('August');
	echo '<option value=9>' . _('September');
	echo '<option value=10>' . _('October');
	echo '<option value=11>' . _('November');
	echo '<option value=12>' . _('December');

	$FSY = $_POST['FSYear'];
	echo '</select>';
	echo "<select name='FSYear'>";
	echo '<option selected="selected" value=$FSY>' . _($FSY);
	for ($yy = 2006;$yy <= 2015;$yy++) {
		echo "<option value=$yy>$yy</option>\n";
	}
	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct SSS') . ":</td><td><select name='SSS'>";
	if ($_POST['SSS'] == 0) {
		echo '<option selected="selected" value=0>' . _('Yes');
		echo '<option value=1>' . _('No');
	} else {
		echo '<option value=0>' . _('Yes');
		echo '<option selected="selected" value=1>' . _('No');
	}

	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct HDMF') . ":</td><td><select name='HDMF'>";
	if ($_POST['HDMF'] == 0) {
		echo '<option selected="selected" value=0>' . _('Yes');
		echo '<option value=1>' . _('No');
	} else {
		echo '<option value=0>' . _('Yes');
		echo '<option selected="selected" value=1>' . _('No');
	}
	echo '</select></td></tr><tr><td width=200 height=20><div align="right"><b>' . _('Deduct PhilHealt') . ":</td><td><select name='PhilHealth'>";
	if ($_POST['PhilHealth'] == 0) {
		echo '<option selected="selected" value=0>' . _('Yes');
		echo '<option value=1>' . _('No');
	} else {
		echo '<option value=0>' . _('Yes');
		echo '<option selected="selected" value=1>' . _('No');
	}
	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New Employee Details') . '"></form>';
	} else {
		//echo "</select></td></tr></table><p><input type="submit" name='submit' value='" . _('Update Payroll Period') . "'>";
		echo '</table><P><input type="submit" name="submit" value="' . _('Update Payroll Period') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete Payroll') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this payroll period?') . '");\"></form>';
	}

}

include ('includes/footer.php');
?>