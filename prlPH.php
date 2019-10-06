<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('PhilHealth Section');

include ('includes/header.php');

if (isset($_GET['Bracket'])) {
	$Bracket = $_GET['Bracket'];
} elseif (isset($_POST['Bracket'])) {

	$Bracket = $_POST['Bracket'];
} else {
	unset($Bracket);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strlen($Bracket) == 0) {
		$InputError = 1;
		prnMsg(_('The Salary Bracket cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (!isset($_POST['New'])) {
			$SQL = "UPDATE prlphilhealth SET
					rangefrom='" . DB_escape_string($_POST['RangeFr']) . "',
					rangeto='" . DB_escape_string($_POST['RangeTo']) . "',
					salarycredit='" . DB_escape_string($_POST['Credit']) . "',
					employerph='" . DB_escape_string($_POST['ERPH']) . "',
					employeeph='" . DB_escape_string($_POST['EEPH']) . "',
					total='" . DB_escape_string($_POST['Total']) . "'
						WHERE bracket='$Bracket'";

			$ErrMsg = _('The PhilHealth could not be updated because');
			$DbgMsg = _('The SQL that was used to update the PhilHealth but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The PhilHealth master record for') . ' ' . $Bracket . ' ' . _('has been updated'), 'success');

		} else { //its a new PhilHealth
			$SQL = "INSERT INTO prlphilhealth (bracket,
					rangefrom,
					rangeto,
					salarycredit,
					employerph,
					employeeph,
					total)
				 VALUES ('$Bracket',
						'" . DB_escape_string($_POST['RangeFr']) . "',
						'" . DB_escape_string($_POST['RangeTo']) . "',
						'" . DB_escape_string($_POST['Credit']) . "',
						'" . DB_escape_string($_POST['ERPH']) . "',
						'" . DB_escape_string($_POST['EEPH']) . "',
						'" . DB_escape_string($_POST['Total']) . "')";
			$ErrMsg = _('The PhilHealth') . ' ' . $_POST['Credit'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the PhilHealth but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new PhilHealth has been added to the database'), 'success');

			unset($Bracket);
			unset($_POST['RangeFr']);
			unset($_POST['RangeTo']);
			unset($_POST['Credit']);
			unset($_POST['ERPH']);
			unset($_POST['EEPH']);
			unset($_POST['Total']);
		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlphilhealth WHERE bracket='$Bracket'";
		$Result = DB_query($SQL);
		prnMsg(_('PhilHealth record for') . ' ' . $Bracket . ' ' . _('has been deleted'), 'success');
		unset($Bracket);
		unset($_SESSION['Bracket']);
	} //end if Delete paypayperiod
	
}

if (!isset($Bracket)) {

	/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('Salary Bracket') . ':</td><td><input type="text" name="Bracket" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Range From') . ':</td><td><input type="text" name="RangeFr" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Range To') . ':</td><td><input type="text" name="RangeTo" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Salary Base') . ':</td><td><input type="text" name="Credit" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Employer Share') . ':</td><td><input type="text" name="ERPH" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Employee Share') . ':</td><td><input type="text" name="EEPH" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Total') . ':</td><td><input type="text" name="Total" size=14 maxlength=12></td></tr>';
	//	echo '</select></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New PhilHealth') . '">';
	echo '</form>';

	$SQL = "SELECT bracket,
					rangefrom,
					rangeto,
					salarycredit,
					employerph,
					employeeph,
					total
				FROM prlphilhealth
				ORDER BY bracket";

	$ErrMsg = _('Could not get PhilHealth because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<th>" . _('Salary Bracket') . "</th>
		<th>" . _('Range From') . "</th>
		<th>" . _('Range To') . "</th>
		<th>" . _('Salary Base') . "</th>
		<th>" . _('Employer Share') . "</th>
		<th>" . _('Employee Share') . "</th>
		<th>" . _('Total') . "</th>
	</tr>";

	$k = 0; //row colour counter
	while ($MyRow = DB_fetch_row($Result)) {

		if ($k == 1) {
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k = 0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k++;
		}
		echo '<td>' . $MyRow[0] . '</td>';
		echo '<td>' . $MyRow[1] . '</td>';
		echo '<td>' . $MyRow[2] . '</td>';
		echo '<td>' . $MyRow[3] . '</td>';
		echo '<td>' . $MyRow[4] . '</td>';
		echo '<td>' . $MyRow[5] . '</td>';
		echo '<td>' . $MyRow[6] . '</td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&Bracket=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&Bracket=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';

} else {
	//Bracket exists - either passed when calling the form or from the form itself
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo '<table>';

	//if (!isset($_POST['New'])) {
	if (!isset($_POST['New'])) {
		$SQL = "SELECT bracket,
					rangefrom,
					rangeto,
					salarycredit,
					employerph,
					employeeph,
					total
				FROM prlphilhealth
				WHERE bracket='$Bracket'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['RangeFr'] = $MyRow['rangefrom'];
		$_POST['RangeTo'] = $MyRow['rangeto'];
		$_POST['Credit'] = $MyRow['salarycredit'];
		$_POST['ERPH'] = $MyRow['employerph'];
		$_POST['EEPH'] = $MyRow['employeeph'];
		$_POST['Total'] = $MyRow['total'];
		echo "<input type=HIDDEN name='Bracket' value='$Bracket'>";

	} else {
		// its a new PhilHealth being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('PhilHealth Code') . ':</td><td><input type="text" name="Bracket" value="', $Bracket, '" size=5 maxlength=4></td></tr>';
	}

	echo '<tr><td>' . _('Range From') . ':</td><td><input type="text" name="RangeFr" size=14 maxlength=12 value="' . $_POST['RangeFr'] . '"></td></tr>';
	echo '<tr><td>' . _('Range To') . ':</td><td><input type="text" name="RangeTo" size=14 maxlength=12 value="' . $_POST['RangeTo'] . '"></td></tr>';
	echo '<tr><td>' . _('Salary Base') . ':</td><td><input type="text" name="Credit" size=14 maxlength=12 value="' . $_POST['Credit'] . '"></td></tr>';
	echo '<tr><td>' . _('Employer Share') . ':</td><td><input type="text" name="ERPH" size=14 maxlength=12 value="' . $_POST['ERPH'] . '"></td></tr>';
	echo '<tr><td>' . _('Employee Share') . ':</td><td><input type="text" name="EEPH" size=14 maxlength=12 value="' . $_POST['EEPH'] . '"></td></tr>';
	echo '<tr><td>' . _('Total') . ':</td><td><input type="text" name="Total" size=14 maxlength=12 value="' . $_POST['Total'] . '"></td></tr>';
	echo '</select></td></tr>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New PhilHealth Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update PhilHealth') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete PhilHealth') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this PhilHealth?') . '");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>