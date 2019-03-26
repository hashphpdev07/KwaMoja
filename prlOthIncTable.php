<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Other Income Section');

include ('includes/header.php');

if (isset($_GET['OthIncID'])) {
	$OthIncID = $_GET['OthIncID'];
} elseif (isset($_POST['OthIncID'])) {

	$OthIncID = $_POST['OthIncID'];
} else {
	unset($OthIncID);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['OthIncDesc'], '&') > 0 or strpos($_POST['OthIncDesc'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The Other Income description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['OthIncDesc']) == '') {
		$InputError = 1;
		prnMsg(_('The Other Income description may not be empty'), 'error');
	}

	if (strlen($OthIncID) == 0) {
		$InputError = 1;
		prnMsg(_('The Other Income Code cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (!isset($_POST['New'])) {

			$SQL = "UPDATE prlothinctable SET othincdesc='" . DB_escape_string($_POST['OthIncDesc']) . "',
							taxable='" . DB_escape_string($_POST['Taxable']) . "'
						WHERE othincid = '$OthIncID'";

			$ErrMsg = _('The other income could not be updated because');
			$DbgMsg = _('The SQL that was used to update the other income but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The other income master record for') . ' ' . $OthIncID . ' ' . _('has been updated'), 'success');

		} else { //its a new other income
			$SQL = "INSERT INTO prlothinctable (othincid,
							othincdesc,
							taxable)
					 VALUES ('$OthIncID',
						'" . DB_escape_string($_POST['OthIncDesc']) . "',
						'" . DB_escape_string($_POST['Taxable']) . "')";

			$ErrMsg = _('The other income') . ' ' . $_POST['OthIncDesc'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the other income but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new other income for') . ' ' . $_POST['OthIncDesc'] . ' ' . _('has been added to the database'), 'success');

			unset($OthIncID);
			unset($_POST['OthIncDesc']);
			unset($_POST['Taxable']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlothinctable WHERE othincid='$OthIncID'";
		$Result = DB_query($SQL);
		prnMsg(_('Other Income record for') . ' ' . $OthIncID . ' ' . _('has been deleted'), 'success');
		unset($OthIncID);
		unset($_SESSION['OthIncID']);
	} //end if Delete paypayperiod
	
}

if (!isset($OthIncID)) {

	/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('Other Income ID') . ':</td><td><input type="text" name="OthIncID" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Other Income Description') . ':</td><td><input type="text" name="OthIncDesc" size=41 maxlength=40></td></tr>';
	echo '</select></td></tr><tr><td width=200 height=20>' . _('Taxable Income ?') . ":</td><td><select name='Taxable'>";
	echo '<option value="Taxable">' . _('Taxable');
	echo '<option value="Non-Tax">' . _('Non-Taxable');
	echo '</select></td></tr>';

	//	echo '</select></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New other income') . '">';
	echo '</form>';

	$SQL = "SELECT othincid,
			othincdesc,
			taxable
			FROM prlothinctable
			ORDER BY othincid";

	$ErrMsg = _('Could not get other income because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<td class='tableheader'>" . _('Other Income ID') . "</td>
		<td class='tableheader'>" . _('Other Income Description') . "</td>
		<td class='tableheader'>" . _('Taxable Income') . "</td>
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
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&OthIncID=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&OthIncID=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';

} else {
	//OthIncID exists - either passed when calling the form or from the form itself
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo '<table>';

	//if (!isset($_POST['New'])) {
	if (!isset($_POST['New'])) {
		$SQL = "SELECT othincid,
				othincdesc,
				taxable
			FROM prlothinctable
			WHERE othincid = '$OthIncID'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['OthIncDesc'] = $MyRow['othincdesc'];
		$_POST['Taxable'] = $MyRow['taxable'];
		echo "<input type=HIDDEN name='OthIncID' value='$OthIncID'>";

	} else {
		// its a new other income being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Other Income Code') . ':</td><td><input type="text" name="OthIncID" value="', $OthIncID, '" size=5 maxlength=4></td></tr>';
	}
	echo "<tr><td>" . _('Other Income Description') . ':' . '</td><td><input type="text" name="OthIncDesc" size=41 maxlength=40 value="' . $_POST['OthIncDesc'] . '"></td></tr>';
	echo '</select></td></tr><tr><td width=200 height=20>' . _('Taxable Income ?') . ":</td><td><select name='Taxable'>";
	if ($_POST['Taxable'] == 'Taxable') {
		echo '<option selected="selected" value="Taxable">' . _('Taxable');
		echo '<option value="Non-Tax">' . _('Non-Taxable');
	} else {
		echo '<option value="Taxable">' . _('Taxable');
		echo '<option selected="selected" value="Non-Tax">' . _('Non-Taxable');
	}
	echo '</select></td></tr>';
	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New Other Income Record') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update Other Income Record') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete this record') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this other income record?') . '\");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>