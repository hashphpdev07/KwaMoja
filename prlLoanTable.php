<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Loan Table Section');

include ('includes/header.php');

if (isset($_GET['LoanTableID'])) {
	$LoanTableID = $_GET['LoanTableID'];
} elseif (isset($_POST['LoanTableID'])) {

	$LoanTableID = $_POST['LoanTableID'];
} else {
	unset($LoanTableID);
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['LoanTableDesc'], '&') > 0 or strpos($_POST['LoanTableDesc'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The loan description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['LoanTableDesc']) == '') {
		$InputError = 1;
		prnMsg(_('The loan description may not be empty'), 'error');
	}

	if (strlen($LoanTableID) == 0) {
		$InputError = 1;
		prnMsg(_('The loan Code cannot be empty'), 'error');
	}

	if ($InputError != 1) {

		if (!isset($_POST['New'])) {

			$SQL = "UPDATE prlloantable SET loantabledesc='" . DB_escape_string($_POST['LoanTableDesc']) . "'
						WHERE loantableid = '$LoanTableID'";

			$ErrMsg = _('The loan could not be updated because');
			$DbgMsg = _('The SQL that was used to update the loan table but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The loan table master record for') . ' ' . $LoanTableID . ' ' . _('has been updated'), 'success');

		} else { //its a new loan record
			$SQL = "INSERT INTO prlloantable (loantableid,
							loantabledesc)
					 VALUES ('$LoanTableID',
						'" . DB_escape_string($_POST['LoanTableDesc']) . "')";

			$ErrMsg = _('The loan') . ' ' . $_POST['LoanTableDesc'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the loan table but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new loan table for') . ' ' . $_POST['LoanTableDesc'] . ' ' . _('has been added to the database'), 'success');

			unset($LoanTableID);
			unset($_POST['LoanTableDesc']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS FOUND
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlloantable WHERE loantableid='$LoanTableID'";
		$Result = DB_query($SQL);
		prnMsg(_('Loan table record for') . ' ' . $LoanTableID . ' ' . _('has been deleted'), 'success');
		unset($LoanTableID);
		unset($_SESSION['LoanTableID']);
	}
}

if (!isset($LoanTableID)) {

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('loan Code') . ':</td><td><input type="text" name="LoanTableID" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Pay Description') . ':</td><td><input type="text" name="LoanTableDesc" size=41 maxlength=40></td></tr>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New loan') . '">';
	echo '</form>';

	$SQL = "SELECT loantableid,
			loantabledesc
			FROM prlloantable
			ORDER BY loantableid";

	$ErrMsg = _('Could not get loan because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<td class='tableheader'>" . _('Loan Code') . "</td>
		<td class='tableheader'>" . _('Loan Description') . "</td>
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
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&LoanTableID=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&LoanTableID=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';

} else {

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo '<table>';

	//if (!isset($_POST['New'])) {
	if (!isset($_POST['New'])) {
		$SQL = "SELECT loantableid,
				loantabledesc
			FROM prlloantable
			WHERE loantableid = '$LoanTableID'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['LoanTableDesc'] = $MyRow['loantabledesc'];
		echo "<input type=HIDDEN name='LoanTableID' value='$LoanTableID'>";

	} else {
		// its a new loan being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Loan Code') . ':</td><td><input type="text" name="LoanTableID" value="', $LoanTableID, '" size=5 maxlength=4></td></tr>';
	}
	echo "<tr><td>" . _('Loan Description') . ':' . '</td><td><input type="text" name="LoanTableDesc" size=41 maxlength=40 value="' . $_POST['LoanTableDesc'] . '"></td></tr>';
	echo '</select></td></tr>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New Loan Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update Loan Table') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete Loan Table') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this loan?') . '");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>