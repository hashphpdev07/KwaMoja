<?php
/* $Revision: 1.0 $ */

include ('includes/prlRegTimeClass.php');

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('Regular Time Entry for Hourly Employees');
include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if ($_GET['NewRT'] == 'Yes' and isset($_SESSION['RTDetail'])) {
	unset($_SESSION['RTDetail']->RTEntries);
	unset($_SESSION['RTDetail']);
}

if (!isset($_SESSION['RTDetail'])) {
	$_SESSION['RTDetail'] = new OverTime;
}
if (!isset($_POST['RTDate'])) {
	$_SESSION['RTDetail']->RTDate = date($_SESSION['DefaultDateFormat']);
}

if (isset($_POST['RTDate'])) {
	$_SESSION['RTDetail']->RTDate = $_POST['RTDate'];
	$AllowThisPosting = true; //by default
	if (!Is_Date($_POST['RTDate'])) {
		prnMsg(_('The date entered was not valid please enter the overtime date') . $_SESSION['DefaultDateFormat'], 'warn');
		$_POST['CommitBatch'] = 'Do not do it the date is wrong';
		$AllowThisPosting = false; //do not allow posting
		
	}
}
$msg = '';

if ($_POST['CommitBatch'] == _('Accept and Process Overtime')) {
	// echo "Start commit Batch";
	//$PeriodNo = GetPeriod($_SESSION['JournalDetail']->JnlDate);
	/*Start a transaction to do the whole lot inside */
	$Result = DB_query('BEGIN');

	//$TransNo = GetNextTransNo( 0);
	foreach ($_SESSION['RTDetail']->RTEntries as $RTItem) {
		$SQL = "INSERT INTO prldailytrans (
						rtref,
						rtdesc,
						rtdate,
						employeeid,
						reghrs)
				VALUES (
					'$RTRef',
					'$RTDesc',
					'" . FormatDateForSQL($_SESSION['RTDetail']->RTDate) . "',
					'" . $RTItem->EmployeeID . "',
					'" . $RTItem->RTHours . "'
					)";
		$ErrMsg = _('Cannot insert regular time entry because');
		$DbgMsg = _('The SQL that failed to insert the regular time Trans record was');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	}

	$ErrMsg = _('Cannot commit the changes');
	$Result = DB_query('COMMIT', $ErrMsg, _('The commit database transaction failed'), true);

	prnMsg(_('Regular Time') . ' ' . $RTDesc . ' ' . _('has been sucessfully entered'), 'success');
	unset($_POST['RTRef']);
	unset($_SESSION['RTDetail']->GLEntries);
	unset($_SESSION['RTDetail']);

	/*Set up a newy in case user wishes to enter another */
	echo "<BR><A HREF='" . basename(__FILE__) . '?' . SID . "&NewRT=Yes'>" . _('Enter Another Overtime Data') . '</A>';
	/*And post the journal too */
	//include ('includes/GLPostings.php');
	exit;
} elseif (isset($_GET['Delete'])) {
	/* User hit delete the line from the ot */
	$_SESSION['RTDetail']->Remove_RTEntry($_GET['Delete']);

	//   $_SESSION['JournalDetail']->Remove_GLEntry($_GET['Delete']);
	
} elseif ($_POST['Process'] == _('Accept')) { //user hit submit a new GL Analysis line into the journal
	if ($AllowThisPosting) {
		$SQL = "SELECT  lastname,firstname
			FROM prlemployeemaster
			WHERE employeeid = '" . $_POST['EmployeeID'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$_SESSION['RTDetail']->Add_RTEntry($_POST['RTHours'], $_POST['EmployeeID'], $MyRow['lastname'], $MyRow['firstname'], $_POST['RTDesc']);
		/*Make sure the same receipt is not double processed by a page refresh */
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
	<td><input type="text" name="RTDate" maxlength=10 size=11 value="' . $_SESSION['RTDetail']->RTDate . '"></td></tr>';
echo '<tr><td>' . _('RT Ref') . ':</td>
	   <td><input type="text" name="RTRef" size="11" maxlength="10" value="' . $_POST['RTRef'] . '"></td></tr>';
echo '</select></td></tr>';
echo '</table></td>'; /*close off the table in the first column */
echo '<td>';
/* Set upthe form for the transaction entry for a GL Payment Analysis item */

echo '<FONT size=3 COLOR=BLUE>' . _('Regular Time Line Entry') . '</FONT><table>';

/*now set up a GLCode field to select from avaialble GL accounts */
echo '<tr><td>' . _('Description') . ':</td><td COLSPAN=3><input type="text" name="RTDesc" size=42 maxlength=40 value="' . $_POST['RTDesc'] . '"></td></tr>';
/*now set up a GLCode field to select from avaialble GL accounts */
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
echo '<tr><td>' . _('Hours') . ":</td><td COLSPAN=3><input type=Text Name='RTHours' Maxlength=12 size=12 value=" . $_POST['RTHours'] . '></td></tr>';
echo '</table>';
echo "<input type=SUBMIT name=Process value='" . _('Accept') . "'><input type=SUBMIT name=Cancel value='" . _('Cancel') . "'>";

echo '</td></tr></table>'; /*Close the main table */

echo "<table WIDTH=100% BORDER=1><tr>
	<td class='tableheader'>" . _('RT Hour') . "</td>
	<td class='tableheader'>" . _('Employee Name') . '</td></tr>';
//<td class='tableheader'>"._('Overtime Type').'</td></tr>';
foreach ($_SESSION['RTDetail']->RTEntries as $RTItem) {
	echo "<tr><td ALIGN=RIGHT>" . number_format($RTItem->RTHours, 2) . "</td>
		<td>" . $RTItem->EmployeeID . ' - ' . $RTItem->LastName . ',' . $RTItem->FirstName . "</td>
		<td><a href='" . basename(__FILE__) . '?' . SID . '&Delete=' . $RTItem->ID . "'>" . _('Delete') . '</a></td>
	</tr>';
}

echo '<tr><td ALIGN=RIGHT><B>' . number_format($_SESSION['RTDetail']->RTTotal, 2) . '</B></td></tr></table>';

if (ABS($_SESSION['RTDetail']->RTTotal) > 0.001 and $_SESSION['RTDetail']->RTItemCounter > 0) {
	echo "<BR><BR><input type=SUBMIT name='CommitBatch' value='" . _('Accept and Process Overtime') . "'>";
}

echo '</form>';
include ('includes/footer.php');
?>