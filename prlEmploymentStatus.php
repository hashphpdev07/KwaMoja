<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include ('includes/session.php');

$Title = _('Employment Status Section');

include ('includes/header.php');

if (isset($_GET['SelectedStatusID'])) $SelectedStatusID = $_GET['SelectedStatusID'];
elseif (isset($_POST['SelectedStatusID'])) $SelectedStatusID = $_POST['SelectedStatusID'];

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strpos($_POST['EmploymentName'], '&') > 0 or strpos($_POST['EmploymentName'], "'") > 0) {
		$InputError = 1;
		prnMsg(_('The employment description cannot contain the character') . " '&' " . _('or the character') . " '", 'error');
	}
	if (trim($_POST['EmploymentName']) == '') {
		$InputError = 1;
		prnMsg(_('The employment description may not be empty'), 'error');
	}

	if ($_POST['SelectedStatusID'] != '' and $InputError != 1) {

		/*SelectedStatusID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
		$SQL = "SELECT count(*) FROM prlemploymentstatus
				WHERE employmentid <> " . $SelectedStatusID . "
				AND employmentdesc " . LIKE . " '" . $_POST['EmploymentName'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The employment description can not be renamed because another with the same name already exist.'), 'error');
		} else {
			// Get the old name and check that the record still exist neet to be very carefull here
			// idealy this is one of those sets that should be in a stored procedure simce even the checks are
			// relavant
			$SQL = "SELECT employmentdesc FROM prlemploymentstatus
				WHERE employmentid = " . $SelectedStatusID;
			$Result = DB_query($SQL);
			if (DB_num_rows($Result) != 0) {
				// This is probably the safest way there is
				$MyRow = DB_fetch_row($Result);
				$OldEmploymentName = $MyRow[0];
				$SQL = array();
				$SQL[] = "UPDATE prlemploymentstatus
					SET employmentdesc='" . DB_escape_string($_POST['EmploymentName']) . "'
					WHERE employmentdesc " . LIKE . " '" . $OldEmploymentName . "'";
				$SQL[] = "UPDATE stockmaster
					SET units='" . DB_escape_string($_POST['EmploymentName']) . "'
					WHERE units " . LIKE . " '" . $OldEmploymentName . "'";
				$SQL[] = "UPDATE contracts
					SET units='" . DB_escape_string($_POST['EmploymentName']) . "'
					WHERE units " . LIKE . " '" . $OldEmploymentName . "'";
			} else {
				$InputError = 1;
				prnMsg(_('The employment description no longer exist.'), 'error');
			}
		}
		$msg = _('Employment description changed');
	} elseif ($InputError != 1) {
		/*SelectedStatusID is null cos no item selected on first time round so must be adding a record*/
		$SQL = "SELECT count(*) FROM prlemploymentstatus
				WHERE employmentdesc " . LIKE . " '" . $_POST['EmploymentName'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The employment description can not be created because another with the same name already exists.'), 'error');
		} else {
			$SQL = "INSERT INTO prlemploymentstatus (
						Employmentdesc )
				VALUES (
					'" . DB_escape_string($_POST['EmploymentName']) . "'
					)";
		}
		$msg = _('New employment description added');
	}

	if ($InputError != 1) {
		//run the SQL from either of the above possibilites
		if (is_array($SQL)) {
			$Result = DB_query('BEGIN');
			$tmpErr = _('Could not update Employment description');
			$tmpDbg = _('The sql that failed was') . ':';
			foreach ($SQL as $stmt) {
				$Result = DB_query($stmt, $tmpErr, $tmpDbg, true);
				if (!$Result) {
					$InputError = 1;
					break;
				}
			}
			if ($InputError != 1) {
				$Result = DB_query('COMMIT');
			} else {
				$Result = DB_query('ROLLBACK');
			}
		} else {
			$Result = DB_query($SQL);
		}
		prnMsg($msg, 'success');
	}
	unset($SelectedStatusID);
	unset($_POST['SelectedStatusID']);
	unset($_POST['EmploymentName']);

} elseif (isset($_GET['delete'])) {
	//the link to delete a selected record was clicked instead of the submit button
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockmaster'
	// Get the original name of the employment status the ID is just a secure way to find the employment status
	$SQL = "SELECT employmentdesc FROM prlemploymentstatus
		WHERE employmentid = " . DB_escape_string($SelectedStatusID);
	$Result = DB_query($SQL);
	if (DB_num_rows($Result) == 0) {
		// This is probably the safest way there is
		prnMsg(_('Cannot delete this employment description because it no longer exist'), 'warn');
	} else {
		$MyRow = DB_fetch_row($Result);
		$OldEmploymentName = $MyRow[0];
		$SQL = "SELECT COUNT(*) FROM stockmaster WHERE units " . LIKE . " '" . DB_escape_string($OldEmploymentName) . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			prnMsg(_('Cannot delete this employment description because inventory items have been created using this employment status'), 'warn');
			echo '<br>' . _('There are') . ' ' . $MyRow[0] . ' ' . _('inventory items that refer to this employment status') . '</FONT>';
		} else {
			$SQL = "SELECT COUNT(*) FROM contracts WHERE units " . LIKE . " '" . DB_escape_string($OldEmploymentName) . "'";
			$Result = DB_query($SQL);
			$MyRow = DB_fetch_row($Result);
			if ($MyRow[0] > 0) {
				prnMsg(_('Cannot delete this employment status because contracts have been created using this employment status'), 'warn');
				echo '<br>' . _('There are') . ' ' . $MyRow[0] . ' ' . _('contracts that refer to this employment status') . '</FONT>';
			} else {
				$SQL = "DELETE FROM prlemploymentstatus WHERE employmentdesc " . LIKE . "'" . DB_escape_string($OldEmploymentName) . "'";
				$Result = DB_query($SQL);
				prnMsg($OldEmploymentName . ' ' . _('employement status has been deleted') . '!', 'success');
			}
		}

	} //end if account group used in GL accounts
	unset($SelectedStatusID);
	unset($_GET['SelectedStatusID']);
	unset($_GET['delete']);
	unset($_POST['SelectedStatusID']);
	unset($_POST['StatusID']);
	unset($_POST['EmploymentName']);
}

if (!isset($SelectedStatusID)) {

	/* An employment status could be posted when one has been edited and is being updated
	 or GOT when selected for modification
	 SelectedStatusID will exist because it was sent with the page in a GET .
	 If its the first time the page has been displayed with no parameters
	 then none of the above are true and the list of account groups will be displayed with
	 links to delete or edit each. These will call the same page again and allow update/input
	 or deletion of the records*/

	$SQL = "SELECT employmentid,
			employmentdesc
			FROM prlemploymentstatus
			ORDER BY employmentid";

	$ErrMsg = _('Could not get employment status because');
	$Result = DB_query($SQL, $ErrMsg);

	echo "<table>
		<tr>
		<td class='tableheader'>" . _('Employment Status') . "</td>
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

		echo '<td>' . $MyRow[1] . '</td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&SelectedStatusID=' . $MyRow[0] . '">' . _('Edit') . '</A></td>';
		echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&SelectedStatusID=' . $MyRow[0] . '&delete=1">' . _('Delete') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><p>';
} //end of ifs and buts!


if (isset($SelectedStatusID)) {
	echo '<A HREF=' . basename(__FILE__) . '?' . SID . '>' . _('Review Employment Status') . '</a></Center>';
}

echo '<P>';

if (!isset($_GET['delete'])) {

	echo "<form method='post' action=" . basename(__FILE__) . '?' . SID . '>';

	if (isset($SelectedStatusID)) {
		//editing an existing section
		$SQL = "SELECT employmentid,
				employmentdesc
				FROM prlemploymentstatus
				WHERE employmentid=" . DB_escape_string($SelectedStatusID);

		$Result = DB_query($SQL);
		if (DB_num_rows($Result) == 0) {
			prnMsg(_('Could not retrieve the requested employment status, please try again.'), 'warn');
			unset($SelectedStatusID);
		} else {
			$MyRow = DB_fetch_array($Result);

			$_POST['StatusID'] = $MyRow['employmentid'];
			$_POST['EmploymentName'] = $MyRow['employmentdesc'];

			echo "<input type=HIDDEN name='SelectedStatusID' value='" . $_POST['StatusID'] . "'>";
			echo "<table>";
		}

	} else {
		$_POST['EmploymentName'] = '';
		echo "<table>";
	}
	echo "<tr>
		<td>" . _('Employment Status') . ':</td>
		<td><input type="text" name="EmploymentName" size=30 maxlength=30 value="' . $_POST['EmploymentName'] . '"></td>
		</tr>';
	echo '</table>';

	echo '<input type=Submit name=submit value=' . _('Enter Information') . '>';

	echo '</form>';

} //end if record deleted no point displaying form to add record
include ('includes/footer.php');
?>