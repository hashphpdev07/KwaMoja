<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Overtime Section');

include ('includes/header.php');

if (isset($_GET['OverTimeID'])) {
	$OverTimeID = $_GET['OverTimeID'];
} elseif (isset($_POST['OverTimeID'])) {

	$OverTimeID = $_POST['OverTimeID'];
} else {
	unset($OverTimeID);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['OverTimeDesc'], '&') > 0 or strpos($_POST['OverTimeDesc'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The overtime description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['OverTimeDesc']) == '') {
		$InputError = 1;
		prnMsg(_('The overtime description may not be empty'), 'error');
	}

	if (strlen($OverTimeID) == 0) {
		$InputError = 1;
		prnMsg(_('The overtime Code cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (!isset($_POST['New'])) {

			$SQL = "UPDATE prlovertimetable SET overtimedesc='" . DB_escape_string($_POST['OverTimeDesc']) . "',
							overtimerate='" . DB_escape_string($_POST['OverTimeRate']) . "'
						WHERE overtimeid = '$OverTimeID'";

			$ErrMsg = _('The overtime could not be updated because');
			$DbgMsg = _('The SQL that was used to update the overtime but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The overtime master record for') . ' ' . $OverTimeID . ' ' . _('has been updated'), 'success');

		} else { //its a new overtime
			$SQL = "INSERT INTO prlovertimetable (overtimeid,
							overtimedesc,
							overtimerate)
					 VALUES ('$OverTimeID',
						'" . DB_escape_string($_POST['OverTimeDesc']) . "',
						'" . DB_escape_string($_POST['OverTimeRate']) . "')";

			$ErrMsg = _('The overtime') . ' ' . $_POST['OverTimeDesc'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the overtime but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new overtime for') . ' ' . $_POST['OverTimeDesc'] . ' ' . _('has been added to the database'), 'success');

			unset($OverTimeID);
			unset($_POST['OverTimeDesc']);
			unset($_POST['OverTimeRate']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlovertimetable WHERE overtimeid='$OverTimeID'";
		$Result = DB_query($SQL);
		prnMsg(_('Overtime record for') . ' ' . $OverTimeID . ' ' . _('has been deleted'), 'success');
		unset($OverTimeID);
		unset($_SESSION['OverTimeID']);
	} //end if Delete paypayperiod
	
}

if (!isset($OverTimeID)) {

	/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('Overtime Code') . ':</td><td><input type="text" name="OverTimeID" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Pay Description') . ':</td><td><input type="text" name="OverTimeDesc" size=41 maxlength=40></td></tr>';
	echo '<tr><td>' . _('Overtime Rate') . ':</td><td><input type="text" name="OverTimeRate" size=7 maxlength=6></td></tr>';
	//	echo '</select></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New Overtime') . '">';
	echo '</form>';

	$SQL = "SELECT overtimeid,
			overtimedesc,
			overtimerate
			FROM prlovertimetable
			ORDER BY overtimeid";

	$ErrMsg = _('Could not get overtime because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<td class='tableheader'>" . _('Overtime Code') . "</td>
		<td class='tableheader'>" . _('Overtime Description') . "</td>
		<td class='tableheader'>" . _('Overtime Rate') . "</td>
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
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&OverTimeID=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&OverTimeID=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';

} else {
	//OverTimeID exists - either passed when calling the form or from the form itself
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo '<table>';

	//if (!isset($_POST['New'])) {
	if (!isset($_POST['New'])) {
		$SQL = "SELECT overtimeid,
				overtimedesc,
				overtimerate
			FROM prlovertimetable
			WHERE overtimeid = '$OverTimeID'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['OverTimeDesc'] = $MyRow['overtimedesc'];
		$_POST['OverTimeRate'] = $MyRow['overtimerate'];
		echo "<input type=HIDDEN name='OverTimeID' value='$OverTimeID'>";

	} else {
		// its a new overtime being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Overtime Code') . ':</td><td><input type="text" name="OverTimeID" value="', $OverTimeID, '" size=5 maxlength=4></td></tr>';
	}
	echo "<tr><td>" . _('Overtime Description') . ':' . '</td><td><input type="text" name="OverTimeDesc" size=41 maxlength=40 value="' . $_POST['OverTimeDesc'] . '"></td></tr>';
	echo "<tr><td>" . _('Overtime Rate') . ':' . '</td><td><input type="text" name="OverTimeRate" size=4 maxlength=6 value="' . $_POST['OverTimeRate'] . '"></td></tr>';
	echo '</select></td></tr>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New overtime Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update overtime') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete overtime') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this overtime?') . '");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>