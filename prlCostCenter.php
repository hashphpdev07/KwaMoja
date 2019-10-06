<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Loan Table Section');

include ('includes/header.php');

if (isset($_GET['CostCenterID'])) {
	$CostCenterID = $_GET['CostCenterID'];
} elseif (isset($_POST['CostCenterID'])) {

	$CostCenterID = $_POST['CostCenterID'];
} else {
	unset($CostCenterID);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['CostCenterDesc'], '&') > 0 or strpos($_POST['CostCenterDesc'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The cost center description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['CostCenterDesc']) == '') {
		$InputError = 1;
		prnMsg(_('The cost center description may not be empty'), 'error');
	}

	if (strlen($CostCenterID) == 0) {
		$InputError = 1;
		prnMsg(_('The cost center Code cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (!isset($_POST['New'])) {

			$SQL = "UPDATE workcentres SET description='" . DB_escape_string($_POST['CostCenterDesc']) . "'
						WHERE code = '$CostCenterID'";

			$ErrMsg = _('The cost center could not be updated because');
			$DbgMsg = _('The SQL that was used to update the cost center table but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The cost center table master record for') . ' ' . $CostCenterID . ' ' . _('has been updated'), 'success');

		} else { //its a new cost center record
			$SQL = "INSERT INTO workcentres (code,
							description)
					 VALUES ('$CostCenterID',
						'" . DB_escape_string($_POST['CostCenterDesc']) . "')";

			$ErrMsg = _('The cost center') . ' ' . $_POST['CostCenterDesc'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the cost center table but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new cost center table for') . ' ' . $_POST['CostCenterDesc'] . ' ' . _('has been added to the database'), 'success');

			unset($CostCenterID);
			unset($_POST['CostCenterDesc']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS FOUND
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM workcentres WHERE code='$CostCenterID'";
		$Result = DB_query($SQL);
		prnMsg(_('cost center table record for') . ' ' . $CostCenterID . ' ' . _('has been deleted'), 'success');
		unset($CostCenterID);
		unset($_SESSION['CostCenterID']);
	}
}

if (!isset($CostCenterID)) {

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('Cost Center Code') . ':</td><td><input type="text" name="CostCenterID" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Pay Description') . ':</td><td><input type="text" name="CostCenterDesc" size=41 maxlength=40></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New Cost Center') . '">';
	echo '</form>';

	$SQL = "SELECT code,
			description
			FROM workcentres
			ORDER BY code";

	$ErrMsg = _('Could not get cost center because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<th>" . _('cost center Code') . "</th>
		<th>" . _('cost center Description') . "</th>
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
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&CostCenterID=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&CostCenterID=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';

} else {

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo '<table>';

	//if (!isset($_POST['New'])) {
	if (!isset($_POST['New'])) {
		$SQL = "SELECT code,
				description
			FROM workcentres
			WHERE code = '$CostCenterID'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['CostCenterDesc'] = $MyRow['description'];
		echo "<input type=HIDDEN name='CostCenterID' value='$CostCenterID'>";

	} else {
		// its a new cost center being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Cost Center Code') . ':</td><td><input type="text" name="CostCenterID" value="', $CostCenterID, '" size=5 maxlength=4></td></tr>';
	}
	echo "<tr><td>" . _('Cost Center Description') . ':</td><td><input type="text" name="CostCenterDesc" size=41 maxlength=40 value="' . $_POST['CostCenterDesc'] . '"></td></tr>';
	echo '</select></td></tr>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New cost center Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update cost center Table') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete cost center Table') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this cost center?') . '");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>