<?php
/* $Revision: 1.0 $ */

include ('includes/prlTardinessClass.php');

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('Late and Absenses Data Entry');
include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if ($_GET['NewTD'] == 'Yes' and isset($_SESSION['TDDetail'])) {
	unset($_SESSION['TDDetail']->TDEntries);
	unset($_SESSION['TDDetail']);
}

if (!isset($_SESSION['TDDetail'])) {
	$_SESSION['TDDetail'] = new Tardiness;
}
if (!isset($_POST['TDDate'])) {
	$_SESSION['TDDetail']->TDDate = date($_SESSION['DefaultDateFormat']);
}

if (isset($_POST['TDDate'])) {
	$_SESSION['TDDetail']->TDDate = $_POST['TDDate'];
	$AllowThisPosting = true; //by default
	if (!Is_Date($_POST['TDDate'])) {
		prnMsg(_('The date entered was not valid please enter the date') . $_SESSION['DefaultDateFormat'], 'warn');
		$_POST['CommitBatch'] = 'Do not do it the date is wrong';
		$AllowThisPosting = false; //do not allow posting
		
	}
}
$msg = '';

if ($_POST['CommitBatch'] == _('Accept and Process Tardiness')) {
	// echo "Start commit Batch";
	//$PeriodNo = GetPeriod($_SESSION['JournalDetail']->JnlDate);
	/*Start a transaction to do the whole lot inside */
	$Result = DB_query('BEGIN');

	foreach ($_SESSION['TDDetail']->TDEntries as $TDItem) {
		$SQL = "INSERT INTO prldailytrans (
						rtref,
						rtdesc,
						rtdate,
						employeeid,
						absenthrs,
						latehrs)
				VALUES (
					'$TDRef',
					'$TDDesc',
					'" . FormatDateForSQL($_SESSION['TDDetail']->TDDate) . "',
					'" . $TDItem->EmployeeID . "',
					'" . $TDItem->TDHoursAbs . "',
					'" . $TDItem->TDHours . "'
					)";
		$ErrMsg = _('Cannot insert entry because');
		$DbgMsg = _('The SQL that failed to insert Trans record was');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	}

	$ErrMsg = _('Cannot commit the changes');
	$Result = DB_query('COMMIT', $ErrMsg, _('The commit database transaction failed'), true);

	prnMsg(_('Late/Absenses') . ' ' . $TDDesc . ' ' . _('has been sucessfully entered'), 'success');
	unset($_POST['TDRef']);
	unset($_SESSION['TDDetail']->TDEntries);
	unset($_SESSION['TDDetail']);

	/*Set up a new in case user wishes to enter another */
	echo "<BR><A HREF='" . basename(__FILE__) . '?' . SID . "&NewTD=Yes'>" . _('Enter Another Late/Absenses Data') . '</A>';
	exit;
} elseif (isset($_GET['Delete'])) {
	/* User hit delete the line from the ot */
	$_SESSION['TDDetail']->Remove_TDEntry($_GET['Delete']);

} elseif ($_POST['Process'] == _('Accept')) {
	if ($AllowThisPosting) {
		$SQL = "SELECT  lastname,firstname
			FROM prlemployeemaster
			WHERE employeeid = '" . $_POST['EmployeeID'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$_SESSION['TDDetail']->Add_TDEntry($_POST['TDHours'], $_POST['TDHoursAbs'], $_POST['EmployeeID'], $MyRow['lastname'], $MyRow['firstname'], $_POST['TDDesc']);
		/*Make sure the same entry is not double processed by a page refresh */
		$Cancel = 1;
	}
}

if (isset($Cancel)) {
	unset($_POST['EmployeeID']);
}

// set up the form whatever
echo '<FORM ACTION=' . basename(__FILE__) . '?' . SID . ' METHOD=POST>';

echo '<P><table BORDER=1 WIDTH=100%>';
echo '<tr><td VALIGN=TOP WIDTH=15%><table>'; // A new table in the first column of the main table
if (!Is_Date($_SESSION['JournalDetail']->JnlDate)) {
	$_SESSION['JournalDetail']->JnlDate = Date($_SESSION['DefaultDateFormat'], mktime(0, 0, 0, date('m'), 0, date('Y')));
}

echo '<tr><td>' . _('Date') . ':</td>
	<td><input type="text" name="TDDate" maxlength=10 size=11 value="' . $_SESSION['TDDetail']->TDDate . '"></td></tr>';
echo '<tr><td>' . _('TD Ref') . ':</td>
	   <td><input type="text" name="TDRef" size="11" maxlength="10" value="' . $_POST['TDRef'] . '"></td></tr>';
echo '</select></td></tr>';
echo '</table></td>'; /*close off the table in the first column */
echo '<td>';
/* Set up the form for the transaction entry */

echo '<FONT size=3 COLOR=BLUE>' . _('Tardiness Time Line Entry - Salaried employees only') . '</FONT><table>';

echo '<tr><td>' . _('Description') . ':</td><td COLSPAN=3><input type="text" name="TDDesc" size=42 maxlength=40 value="' . $_POST['TDDesc'] . '"></td></tr>';
echo '<tr><td>' . _('Enter Employee Manually') . ":</td>
	<td><input type=Text Name='EmployeeManualCode' Maxlength=12 size=12 value=" . $_POST['EmployeeManualCode'] . '></td>';
echo '<td>' . _('OR') . ' ' . _('Select Employee Name') . ":</td><td><select name='EmployeeID'>";
$SQL = 'SELECT employeeid, lastname, firstname FROM prlemployeemaster ORDER BY employeeid';
$Result = DB_query($SQL);
if (DB_num_rows($Result) == 0) {
	echo '</select></td></tr>';
	prnMsg(_('No Empoloyee accounts have been set up yet'), 'warn');
} else {
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['EmployeeID'] == $MyRow['employeeid']) {
			echo '<option selected="selected" value=' . $MyRow['employeeid'] . '>' . $MyRow['lastname'] . ',' . $MyRow['firstname'];
		} else {
			echo '<option value=' . $MyRow['employeeid'] . '>' . $MyRow['lastname'] . ',' . $MyRow['firstname'];
		}
	} //end while loop
	echo '</select></td></tr>';
}
echo '<tr><td>' . _('Late(s) - Hour(s)') . ":</td><td COLSPAN=3><input type=Text Name='TDHours' Maxlength=12 size=12 value=" . $_POST['TDHours'] . '></td></tr>';
echo '<tr><td>' . _('Absent - Hours') . ":</td><td COLSPAN=3><input type=Text Name='TDHoursAbs' Maxlength=12 size=12 value=" . $_POST['TDHoursAbs'] . '></td></tr>';
echo '</table>';
echo "<input type=SUBMIT name=Process value='" . _('Accept') . "'><input type=SUBMIT name=Cancel value='" . _('Cancel') . "'>";

echo '</td></tr></table>'; /*Close the main table */

echo "<table WIDTH=100% BORDER=1><tr>
	<td class='tableheader'>" . _('Late(s)- Hour(s)') . "</td>
	<td class='tableheader'>" . _('Absent - Hour(s)') . "</td>
	<td class='tableheader'>" . _('Employee Name') . '</td></tr>';

foreach ($_SESSION['TDDetail']->TDEntries as $TDItem) {
	echo "<tr><td ALIGN=RIGHT>" . number_format($TDItem->TDHours, 2) . "</td>
        <td ALIGN=RIGHT>" . number_format($TDItem->TDHoursAbs, 2) . "</td>
		<td>" . $TDItem->EmployeeID . ' - ' . $TDItem->LastName . ',' . $TDItem->FirstName . "</td>
		<td><a href='" . basename(__FILE__) . '?' . SID . '&Delete=' . $TDItem->ID . "'>" . _('Delete') . '</a></td>
	</tr>';
}

echo '<tr><td ALIGN=RIGHT><B>' . number_format($_SESSION['TDDetail']->TDTotal, 2) . '</B></td><td ALIGN=RIGHT><B>' . number_format($_SESSION['TDDetail']->TDTotalAbs, 2) . '</B></td></tr></table>';

if ((ABS($_SESSION['TDDetail']->TDTotal) > 0.001 and $_SESSION['TDDetail']->TDItemCounter > 0) or (ABS($_SESSION['TDDetail']->TDTotalAbs) > 0.001 and $_SESSION['TDDetail']->TDItemCounter > 0)) {
	echo "<BR><BR><input type=SUBMIT name='CommitBatch' value='" . _('Accept and Process Tardiness') . "'>";
}

echo '</form>';
include ('includes/footer.php');
?>