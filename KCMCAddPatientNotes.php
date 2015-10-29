<?php

include('includes/session.inc');
$Title = _('Patient Notes');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/CustomerSearch.php');

if (isset($_GET['Id'])) {
	$Id = (int) $_GET['Id'];
} else if (isset($_POST['Id'])) {
	$Id = (int) $_POST['Id'];
}
if (isset($_POST['DebtorNo'])) {
	$Patient[0] = $_POST['DebtorNo'];
} elseif (isset($_GET['DebtorNo'])) {
	$Patient[0] = stripslashes($_GET['DebtorNo']);
}

if (!isset($_POST['Search']) and !isset($_POST['Next']) and !isset($_POST['Previous']) and !isset($_POST['Go1']) and !isset($_POST['Go2']) and isset($_POST['JustSelectedACustomer']) and empty($_POST['Patient'])) {
	/*Need to figure out the number of the form variable that the user clicked on */
	for ($i = 0; $i < count($_POST); $i++) { //loop through the returned customers
		if (isset($_POST['SubmitCustomerSelection' . $i])) {
			break;
		}
	}
	if ($i == count($_POST)) {
		prnMsg(_('Unable to identify the selected customer'), 'error');
	} else {
		$Patient[0] = $_POST['SelectedCustomer' . $i];
		$Patient[1] = $_POST['SelectedBranch' . $i];
		unset($_POST['Search']);
	}
}

if (!isset($Patient)) {
	ShowCustomerSearchFields($RootPath, $_SESSION['Theme']);
}

if (isset($_POST['Search']) or isset($_POST['Go1']) or isset($_POST['Go2']) or isset($_POST['Next']) or isset($_POST['Previous'])) {

	$PatientResult = CustomerSearchSQL();
	if (DB_num_rows($PatientResult) == 0) {
		prnMsg(_('No patient records contain the selected text') . ' - ' . _('please alter your search criteria and try again'), 'info');
		echo '<br />';
	}
} //end of if search

if (isset($PatientResult)) {
	ShowReturnedCustomers($PatientResult);
}

if (isset($Patient)) {
	echo '<div class="toplink"><a href="' . $RootPath . '/KCMCAddPatientNotes.php">' . _('Select Another Patient') . '</a></div>';

	if (isset($_POST['submit'])) {

		//initialise no input errors assumed initially before we test
		$InputError = 0;
		/* actions to take once the user has clicked the submit button
		ie the page has called itself with some user input */

		//first off validate inputs sensible
		if (!is_long((integer) $_POST['Priority'])) {
			$InputError = 1;
			prnMsg(_('The contact priority must be an integer.'), 'error');
		} elseif (mb_strlen($_POST['Note']) > 200) {
			$InputError = 1;
			prnMsg(_('The contact\'s notes must be two hundred characters or less long'), 'error');
		} elseif (trim($_POST['Note']) == '') {
			$InputError = 1;
			prnMsg(_('The contact\'s notes may not be empty'), 'error');
		}

		if (isset($Id) and $InputError != 1) {

			$SQL = "UPDATE custnotes SET note='" . $_POST['Note'] . "',
									date='" . FormatDateForSQL($_POST['NoteDate']) . "',
									priority='" . $_POST['Priority'] . "'
				WHERE debtorno ='" . $Patient[0] . "'
				AND noteid='" . $Id . "'";
			$Msg = _('Customer Notes') . ' ' . $Patient[0] . ' ' . _('has been updated');
		} elseif ($InputError != 1) {

			$SQL = "INSERT INTO custnotes (debtorno,
											userid,
											note,
											date,
											priority)
										VALUES (
											'" . $_POST['DebtorNo'] . "',
											'" . $_SESSION['UserID'] . "',
											'" . $_POST['Note'] . "',
											'" . FormatDateForSQL($_POST['NoteDate']) . "',
											'" . $_POST['Priority'] . "')";
			$Msg = _('The contact notes record has been added');
		}

		if ($InputError != 1) {
			$Result = DB_query($SQL);

			prnMsg($Msg, 'success');
			unset($Id);
			unset($_POST['Note']);
			unset($_POST['Noteid']);
			unset($_POST['NoteDate']);
			unset($_POST['Priority']);
		}
	} elseif (isset($_GET['delete'])) {
		//the link to delete a selected record was clicked instead of the submit button

		// PREVENT DELETES IF DEPENDENT RECORDS IN 'SalesOrders'

		$SQL = "DELETE FROM custnotes
			WHERE noteid='" . $Id . "'
			AND debtorno='" . $Patient[0] . "'";
		$Result = DB_query($SQL);

		prnMsg(_('The contact note record has been deleted'), 'success');
		unset($Id);
		unset($_GET['delete']);
	}

	if (!isset($Id)) {
		$NameSql = "SELECT * FROM debtorsmaster
				WHERE debtorno='" . $Patient[0] . "'";
		$Result = DB_query($NameSql);
		$MyRow = DB_fetch_array($Result);
		echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/maintenance.png" title="' . _('Search') . '" alt="" />' . _('Notes for Patient') . ': <b>' . $MyRow['name'] . '</b></p>';

		$SQL = "SELECT noteid,
						debtorno,
						note,
						date,
						priority,
						realname
					FROM custnotes
					INNER JOIN www_users
						ON custnotes.userid=www_users.userid
					WHERE debtorno='" . $Patient[0] . "'
					ORDER BY date DESC";
		$Result = DB_query($SQL);

		echo '<table class="selection">
		<tr>
			<th>' . _('Date') . '</th>
			<th>' . _('Doctor') . '</th>
			<th>' . _('Note') . '</th>
			<th>' . _('Priority') . '</th>
		</tr>';

		$k = 0; //row colour counter

		while ($MyRow = DB_fetch_array($Result)) {
			if ($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="EvenTableRows">';
				$k = 1;
			}
			printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td><a href="%sId=%s&DebtorNo=%s">' . _('Edit') . ' </td>
					<td><a href="%sId=%s&DebtorNo=%s&delete=1" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this customer note?') . '\', \'Confirm Delete\', this);">' . _('Delete') . '</td></tr>', ConvertSQLDate($MyRow['date']), $MyRow['realname'], $MyRow['note'], $MyRow['priority'], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $MyRow['noteid'], urlencode($MyRow['debtorno']), htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $MyRow['noteid'], urlencode($MyRow['debtorno']));

		}
		//END WHILE LIST LOOP
		echo '</table>';
	}
	if (isset($Id)) {
		echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?DebtorNo=' . urlencode($Patient[0]) . '">' . _('Review all notes for this Customer') . '</a>
		</div>';
	}

	if (!isset($_GET['delete'])) {

		echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?DebtorNo=' . $Patient[0] . '">';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

		if (isset($Id)) {
			//editing an existing

			$SQL = "SELECT noteid,
						debtorno,
						href,
						note,
						date,
						priority
					FROM custnotes
					WHERE noteid='" . $Id . "'
						AND debtorno='" . $Patient[0] . "'";

			$Result = DB_query($SQL);

			$MyRow = DB_fetch_array($Result);

			$_POST['Noteid'] = $MyRow['noteid'];
			$_POST['Note'] = $MyRow['note'];
			$_POST['Href'] = $MyRow['href'];
			$_POST['NoteDate'] = $MyRow['date'];
			$_POST['Priority'] = $MyRow['priority'];
			$_POST['debtorno'] = $MyRow['debtorno'];
			echo '<input type="hidden" name="Id" value="' . $Id . '" />';
			echo '<input type="hidden" name="Con_ID" value="' . $_POST['Noteid'] . '" />';
			echo '<input type="hidden" name="DebtorNo" value="' . $_POST['debtorno'] . '" />';
			echo '<table class="selection">
			<tr>
				<td>' . _('Note ID') . ':</td>
				<td>' . $_POST['Noteid'] . '</td>
			</tr>';
		} else {
			echo '<table class="selection">';
		}

		echo '<input type="hidden" name="DebtorNo" value="' . stripslashes(stripslashes($Patient[0])) . '" />';
		echo '<tr>
			<td>' . _('Note') . '</td>';
		if (isset($_POST['Note'])) {
			echo '<td><textarea name="Note" rows="3" required="required" minlength="1" cols="32">' . $_POST['Note'] . '</textarea></td>
			</tr>';
		} else {
			echo '<td><textarea name="Note" rows="3" cols="32"></textarea></td>
			</tr>';
		}
		echo '<tr>
			<td>' . _('Date') . '</td>';
		if (isset($_POST['date'])) {
			echo '<td><input type="text" name="NoteDate" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" id="datepicker" value="' . ConvertSQLDate($_POST['date']) . '" size="10" minlength="0" maxlength="10" /></td>
			</tr>';
		} else {
			echo '<td><input type="text" name="NoteDate" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" id="datepicker" value="' . date($_SESSION['DefaultDateFormat']) . '" size="10" minlength="0" maxlength="10" /></td>
			</tr>';
		}
		echo '<tr>
			<td>' . _('Priority') . '</td>';
		if (isset($_POST['Priority'])) {
			echo '<td><input type="text" class=integer" name="Priority" value="' . $_POST['Priority'] . '" size="1" minlength="0" maxlength="3" /></td>
			</tr>';
		} else {
			echo '<td><input type="text" class="integer" name="Priority" size="1" minlength="0" maxlength="3" /></td>
			</tr>';
		}
		echo '<tr>
			<td colspan="2">
			<div class="centre">
				<input type="submit" name="submit" value="' . _('Enter Information') . '" />
			</div>
			</td>
		</tr>
		</table>
		</form>';

	} //end if record deleted no point displaying form to add record
}
include('includes/footer.inc');
?>