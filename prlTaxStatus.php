<?php
/* $Revision: 1.16 $ */

$PageSecurity = 5;

include ('includes/session.php');

$Title = _('Employee Maintenance');

include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if (isset($_GET['TaxStatusID'])) {
	$TaxStatusID = strtoupper($_GET['TaxStatusID']);
} elseif (isset($_POST['TaxStatusID'])) {
	$TaxStatusID = strtoupper($_POST['TaxStatusID']);
} else {
	unset($TaxStatusID);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if ($InputError != 1) {

		if (!isset($_POST['New'])) {

			$SQL = "UPDATE prltaxstatus SET
					taxstatusdescription='" . DB_escape_string($_POST['TaxStatusDescription']) . "',
					personalexemption='" . DB_escape_string($_POST['PersonalExemption']) . "',
					additionalexemption='" . DB_escape_string($_POST['AdditionalExemption']) . "',
					totalexemption='" . DB_escape_string($_POST['TotalExemption']) . "'
                WHERE taxstatusid = '$TaxStatusID'";
			$ErrMsg = _('The tax status could not be updated because');
			$DbgMsg = _('The SQL that was used to update the tax status but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The tax status master record for') . ' ' . $TaxStatusID . ' ' . _('has been updated'), 'success');

		} else { //its a new tax status
			$SQL = "INSERT INTO prltaxstatus (
					taxstatusid,
					taxstatusdescription,
					personalexemption,
					additionalexemption,
					totalexemption)
				VALUES ('$TaxStatusID',
					'" . DB_escape_string($_POST['TaxStatusDescription']) . "',
					'" . DB_escape_string($_POST['PersonalExemption']) . "',
					'" . DB_escape_string($_POST['AdditionalExemption']) . "',
					'" . DB_escape_string($_POST['TotalExemption']) . "'
					)";
			$ErrMsg = _('The tax status') . ' ' . $_POST['TaxStatusDescription'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the tax status but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new tax status for') . ' ' . $_POST['TaxStatusDescription'] . ' ' . _('has been added to the database'), 'success');

			unset($TaxStatusID);
			unset($_POST['TaxStatusDescription']);
			unset($_POST['PersonalExemption']);
			unset($_POST['AdditionalExemption']);
			unset($_POST['TotalExemption']);
		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prltaxstatus WHERE taxstatusid='$TaxStatusID'";
		$Result = DB_query($SQL);
		prnMsg(_('Tax status record for') . ' ' . $TaxStatusID . ' ' . _('has been deleted'), 'success');
		unset($TaxStatusID);
		unset($_SESSION['TaxStatusID']);
	} //end if Delete tax status
	
} //end of (isset($_POST['submit']))


if (!isset($TaxStatusID)) {
	/*If the page was called without $EmployeeID passed to page then assume a new employee is to be entered show a form
	with a Employee Code field other wise the form showing the fields with the existing entries against the employee will
	show for editing with only a hidden EmployeeID field*/
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo "<input type='hidden' name='New' value='Yes'>";
	echo '<table>';
	//echo "me her";
	echo '<tr><td>' . _('Tax Status ID') . ':</td>
	     <td><input type="text" name="TaxStatusID" size=11 maxlength=10></td></tr>';
	echo '<tr><td>' . _('Tax Status Description') . ':</td>
		<td><input type="text" name="TaxStatusDescription" size=41 maxlength=40></td></tr>';
	echo '<tr><td>' . _('Personal Exemption') . ':</td>
		<td><input type="text" name="PersonalExemption" size=13 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Additional Exemption') . ':</td>
		<td><input type="TotalExemptionText" name="AdditionalExemption" size=13 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Total Exemption') . ':</td>
		<td><input type="text" name="TotalExemption" size=13 maxlength=12></td></tr>';
	//echo'</table>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New Tax Status') . '">';
	echo '</form>';

} else {
	//SupplierID exists - either passed when calling the form or from the form itself
	echo "<form method='post' action='" . basename(__FILE__) . '?' . SID . "'>";
	echo '<table>';

	if (!isset($_POST['New'])) {

		$SQL = "SELECT  taxstatusid,
						taxstatusdescription,
						personalexemption,
						additionalexemption,
						totalexemption
			FROM prltaxstatus
			WHERE taxstatusid = '$TaxStatusID'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$_POST['TaxStatusDescription'] = $MyRow['taxstatusdescription'];
		$_POST['PersonalExemption'] = $MyRow['personalexemption'];
		$_POST['AdditionalExemption'] = $MyRow['additionalexemption'];
		$_POST['TotalExemption'] = $MyRow['totalexemption'];

		echo "<input type=HIDDEN name='TaxStatusID' value='$TaxStatusID'>";
	} else {
		// its a new supplier being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Tax Status ID') . ':</td><td><input type="text" name="TaxStatusID" value="', $TaxStatusID, '" size=12 maxlength=10></td></tr>';
	}
	echo '<tr><td>' . _('Tax Status Description') . ':</td>
		<td><input type="text" name="TaxStatusDescription" value="' . $_POST['TaxStatusDescription'] . '" size=42 maxlength=40></td></tr>';
	echo '<tr><td>' . _('Personal Exemption') . ':</td>
		<td><input type="text" name="PersonalExemption" value="' . $_POST['PersonalExemption'] . '" size=13 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Additional Exemption') . ':</td>
		<td><input type="text" name="AdditionalExemption" size=13 maxlength=12 value="' . $_POST['AdditionalExemption'] . '"></td></tr>';
	echo '<tr><td>' . _('Total Exemption') . ':</td>
		<td><input type="text" name="TotalExemption" size=13 maxlength=12 value="' . $_POST['TotalExemption'] . '"></td></tr>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New Tax Status Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update Tax Status') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete Employee') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this tax status?') . '");\"></form>';
		//echo "<BR><A HREF='$RootPath/SupplierContacts.php?" . SID . "SupplierID=$SupplierID'>" . _('Review Contact Details') . '</A>';
		
	}

} // end of main ifs
include ('includes/footer.php');
?>