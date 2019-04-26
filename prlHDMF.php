<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Pag-ibig Section');

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

			$SQL = "UPDATE prlhdmftable SET
					rangefrom='" . DB_escape_string($_POST['RangeFr']) . "',
					rangeto='" . DB_escape_string($_POST['RangeTo']) . "',
					dedtypeer='" . $_POST['DedTypeER'] . "',
					employershare='" . DB_escape_string($_POST['ERHDMF']) . "',
					dedtypeee='" . $_POST['DedTypeEE'] . "',
					employeeshare='" . DB_escape_string($_POST['EEHDMF']) . "'
						WHERE bracket='$Bracket'";

			$ErrMsg = _('The Pag-ibig could not be updated because');
			$DbgMsg = _('The SQL that was used to update the Pag-ibig but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);
			prnMsg(_('The Pag-ibig master record for') . ' ' . $Bracket . ' ' . _('has been updated'), 'success');

		} else { //its a new Pag-ibig
			$SQL = "INSERT INTO prlhdmftable (bracket,
					rangefrom,
					rangeto,
					dedtypeer,
					employershare,
					dedtypeee,
					employeeshare)
				 VALUES ('$Bracket',
						'" . DB_escape_string($_POST['RangeFr']) . "',
						'" . DB_escape_string($_POST['RangeTo']) . "',
						'" . $_POST['DedTypeER'] . "',
						'" . DB_escape_string($_POST['ERHDMF']) . "',
						'" . $_POST['DedTypeEE'] . "',
						'" . DB_escape_string($_POST['EEHDMF']) . "')";
			$ErrMsg = _('The Pag-ibig could not be added because');
			$DbgMsg = _('The SQL that was used to insert the Pag-ibig but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new Pag-ibig has been added to the database'), 'success');

			unset($Bracket);
			unset($_POST['RangeFr']);
			unset($_POST['RangeTo']);
			unset($_POST['DedTypeER']);
			unset($_POST['ERHDMF']);
			unset($_POST['DedTypeEE']);
			unset($_POST['EEHDMF']);
		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS found
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM prlhdmftable WHERE bracket='$Bracket'";
		$Result = DB_query($SQL);
		prnMsg(_('Pag-ibig record for') . ' ' . $Bracket . ' ' . _('has been deleted'), 'success');
		unset($Bracket);
		unset($_SESSION['Bracket']);
	}
}

if (!isset($Bracket)) {

	/*new hdmf*/

	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";

	echo "<input type='hidden' name='New' value='Yes'>";

	echo '<table>';
	echo '<tr><td>' . _('Salary Bracket') . ':</td><td><input type="text" name="Bracket" size=5 maxlength=4></td></tr>';
	echo '<tr><td>' . _('Range From') . ':</td><td><input type="text" name="RangeFr" size=14 maxlength=12></td></tr>';
	echo '<tr><td>' . _('Range To') . ':</td><td><input type="text" name="RangeTo" size=14 maxlength=12></td></tr>';
	echo '</select></td></tr><tr><td>' . _('Employer Share') . ':</td><td><select name="DedTypeER">';
	echo '<option value="Fixed">' . _('Fixed');
	echo '<option value="Percentage">' . _('Percentage');
	echo '<td><input type="text" name="ERHDMF" size=14 maxlength=12></td>';
	echo '</select></td></tr><tr><td>' . _('Employee Share') . ':</td><td><select name="DedTypeEE">';
	echo '<option value="Fixed">' . _('Fixed');
	echo '<option value="Percentage">' . _('Percentage');
	echo '<td><input type="text" name="EEHDMF" size=14 maxlength=12></td>';
	echo '</select></td></tr></table><p><input type="submit" name="submit" value="' . _('Insert New Pag-ibig') . '"';
	echo '</form>';

	$SQL = "SELECT bracket,
					rangefrom,
					rangeto,
					dedtypeer,
					employershare,
					dedtypeee,
					employeeshare
				FROM prlhdmftable
				ORDER BY bracket";

	$ErrMsg = _('Could not get Pag-ibig because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<td class='tableheader'>" . _('Salary Bracket') . "</td>
		<td class='tableheader'>" . _('Range From') . "</td>
		<td class='tableheader'>" . _('Range To') . "</td>
		<td class='tableheader'>" . _('Employer Share') . "</td>
		<td class='tableheader'>" . _('Employer Share') . "</td>
		<td class='tableheader'>" . _('Employee Share') . "</td>
		<td class='tableheader'>" . _('Employee Share') . "</td>
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
					dedtypeer,
					employershare,
					dedtypeee,
					employeeshare
				FROM prlhdmftable
				WHERE bracket='$Bracket'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['RangeFr'] = $MyRow['rangefrom'];
		$_POST['RangeTo'] = $MyRow['rangeto'];
		$_POST['DedTypeER'] = $MyRow['dedtypeer'];
		$_POST['ERHDMF'] = $MyRow['employershare'];
		$_POST['DedTypeEE'] = $MyRow['dedtypeee'];
		$_POST['EEHDMF'] = $MyRow['employeeshare'];
		echo "<input type=HIDDEN name='Bracket' value='$Bracket'>";

	} else {
		// its a new Pag-ibig being added
		echo "<input type=HIDDEN name='New' value='Yes'>";
		echo '<tr><td>' . _('Bracket') . ':</td><td><input type="text" name="Bracket" value="', $Bracket, '" size=5 maxlength=4></td></tr>';
	}
	echo '<tr><td>' . _('Range From') . ':</td><td><input type="text" name="RangeFr" size=14 maxlength=12 value="' . $_POST['RangeFr'] . '"></td></tr>';
	echo '<tr><td>' . _('Range To') . ':</td><td><input type="text" name="RangeTo" size=14 maxlength=12 value="' . $_POST['RangeTo'] . '"></td></tr>';
	echo '</select></td></tr><tr><td>' . _('Employer Share') . ":</td><td><select name='DedTypeER'>";
	if ($_POST['DedTypeER'] == 'Fixed') {
		echo '<option selected="selected" value="Fixed">' . _('Fixed');
		echo '<option value="Percentage">' . _('Percentage');
	} elseif ($_POST['DedTypeER'] == 'Percentage') {
		echo '<option selected="selected" value="Percentage">' . _('Percentage');
		echo '<option value="Fixed">' . _('Fixed');
	} else {
		echo '<option selected="selected" value="">' . _('Select One');
		echo '<option value="Fixed">' . _('Fixed');
		echo '<option value="Percentage">' . _('Percentage');
	}
	echo '<td><input type="text" name="ERHDMF" size=14 maxlength=12 value="' . $_POST['ERHDMF'] . '"></td>';

	echo '</select></td></tr><tr><td>' . _('Employee Share') . ":</td><td><select name='DedTypeEE'>";
	if ($_POST['DedTypeEE'] == 'Fixed') {
		echo '<option selected="selected" value="Fixed">' . _('Fixed');
		echo '<option value="Percentage">' . _('Percentage');
	} elseif ($_POST['DedTypeEE'] == 'Percentage') {
		echo '<option selected="selected" value="Percentage">' . _('Percentage');
		echo '<option value="Fixed">' . _('Fixed');
	} else {
		echo '<option selected="selected" value="">' . _('Select One');
		echo '<option value="Fixed">' . _('Fixed');
		echo '<option value="Percentage">' . _('Percentage');
	}
	echo '<td><input type="text" name="EEHDMF" size=14 maxlength=12 value="' . $_POST['EEHDMF'] . '"></td>';

	if (isset($_POST['New'])) {
		echo '</table><P><input type="submit" name="submit" value="' . _('Add These New Pag-ibig Details') . '"></form>';
	} else {
		echo '</table><P><input type="submit" name="submit" value="' . _('Update Pag-ibig') . '">';
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure before the deletion is processed') . '<BR></FONT></B>';
		echo '<input type="submit" name="delete" value="' . _('Delete Pag-ibig') . '" onclick=\"return confirm("' . _('Are you sure you wish to delete this Pag-ibig?') . '");\"></form>';
	}

} // end of main ifs
include ('includes/footer.php');
?>