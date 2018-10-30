<?php
/* $Revision: 1.0 $ */

include ('includes/prlOthIncomeClass.php');

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('Other Income Data Entry');
include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');
include ('includes/prlFunctions.php');

if ($_GET['NewOI'] == 'Yes' and isset($_SESSION['OIDetail'])) {
	unset($_SESSION['OIDetail']->OIEntries);
	unset($_SESSION['OIDetail']);
}

if (!isset($_SESSION['OIDetail'])) {
	$_SESSION['OIDetail'] = new OthIncome;
}
if (!isset($_POST['OIDate'])) {
	$_SESSION['OIDetail']->OIDate = date($_SESSION['DefaultDateFormat']);
}

if (isset($_POST['OIDate'])) {
	$_SESSION['OIDetail']->OIDate = $_POST['OIDate'];
	$AllowThisPosting = true; //by default
	if (!Is_Date($_POST['OIDate'])) {
		prnMsg(_('The date entered was not valid please enter the date') . $_SESSION['DefaultDateFormat'], 'warn');
		$_POST['CommitBatch'] = 'Do not do it the date is wrong';
		$AllowThisPosting = false; //do not allow posting
		
	}
}
$msg = '';

if ($_POST['CommitBatch'] == _('Accept and Process Other Income')) {

	/*Start a transaction to do the whole lot inside */
	$Result = DB_query('BEGIN');

	foreach ($_SESSION['OIDetail']->OIEntries as $OIItem) {
		$SQL = "INSERT INTO prlothincfile (
						othfileref,
						othfiledesc,
						employeeid,
						othdate,
						othincid,
						othincamount)
				VALUES (
					'$OIRef',
					'$OIDesc',
					'" . $OIItem->EmployeeID . "',
					'" . FormatDateForSQL($_SESSION['OIDetail']->OIDate) . "',
					'" . $OIItem->OIID . "',
					'" . $OIItem->Amount . "'
					)";
		$ErrMsg = _('Cannot insert entry because');
		$DbgMsg = _('The SQL that failed to insert Trans record was');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	}

	$ErrMsg = _('Cannot commit the changes');
	$Result = DB_query('COMMIT', $ErrMsg, _('The commit database transaction failed'), true);

	prnMsg(_('Other Income') . ' ' . $OIDesc . ' ' . _('has been sucessfully entered'), 'success');
	unset($_POST['OIRef']);
	unset($_SESSION['OIDetail']->OIEntries);
	unset($_SESSION['OIDetail']);

	/*Set up a new in case user wishes to enter another */
	echo "<BR><A HREF='" . basename(__FILE__) . '?' . SID . "&NewOI=Yes'>" . _('Enter Other Income Data') . '</A>';
	exit;
} elseif (isset($_GET['Delete'])) {
	/* User hit delete the line from the ot */
	$_SESSION['OIDetail']->Remove_OIEntry($_GET['Delete']);

} elseif ($_POST['Process'] == _('Accept')) {
	if ($AllowThisPosting) {
		$OIIDDesc = GetOthIncRow($_POST['OthIncID'], 0);
		$SQL = "SELECT  lastname,firstname
			FROM prlemployeemaster
			WHERE employeeid = '" . $_POST['EmployeeID'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$_SESSION['OIDetail']->Add_OIEntry($_POST['Amount'], $_POST['EmployeeID'], $MyRow['lastname'], $MyRow['firstname'], $_POST['OthIncID'], $OIIDDesc);
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
	<td><input type="text" name="OIDate" maxlength=10 size=11 value="' . $_SESSION['OIDetail']->OIDate . '"></td></tr>';
echo '<tr><td>' . _('Ref') . ':</td>
	   <td><input type="text" name="OIRef" size="11" maxlength="10" value="' . $_POST['OIRef'] . '"></td></tr>';
echo '</select></td></tr>';
echo '</table></td>'; /*close off the table in the first column */
echo '<td>';
/* Set up the form for the transaction entry */

echo '<FONT size=3 COLOR=BLUE>' . _('Other Income Line Entry') . '</FONT><table>';

echo '<tr><td>' . _('Description') . ':</td><td COLSPAN=3><input type="text" name="OIDesc" size=42 maxlength=40 value="' . $_POST['OIDesc'] . '"></td></tr>';
echo '<tr><td>' . _('Employee ') . ":</td>
	<td><input type=Text Name='EmployeeManualCode' Maxlength=12 size=12 value=" . $_POST['EmployeeManualCode'] . '></td>';
echo "<td><select name='EmployeeID'>";
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
echo '<tr><td>' . _('Other Income Type') . ":</td><td><select name='OthIncID'>";
DB_data_seek($Result, 0);
$SQL = 'SELECT othincid, othincdesc FROM prlothinctable';
$Result = DB_query($SQL);
while ($MyRow = DB_fetch_array($Result)) {
	if ($_POST['OthIncID'] == '') {
		echo '<option selected="selected" value=' . $MyRow['othincid'] . '>' . $MyRow['othincdesc'];
	} else {
		echo '<option value=' . $MyRow['othincid'] . '>' . $MyRow['othincdesc'];
	}
} //end while loop
echo '<tr><td>' . _('Amount') . ":</td><td COLSPAN=3><input type=Text Name='Amount' Maxlength=12 size=12 value=" . $_POST['Amount'] . '></td></tr>';
echo '</table>';
echo "<input type=SUBMIT name=Process value='" . _('Accept') . "'><input type=SUBMIT name=Cancel value='" . _('Cancel') . "'>";

echo '</td></tr></table>'; /*Close the main table */

echo "<table WIDTH=100% BORDER=1><tr>
	<td class='tableheader'>" . _('Amount') . "</td>
	<td class='tableheader'>" . _('Description') . "</td>
	<td class='tableheader'>" . _('Employee Name') . '</td></tr>';

foreach ($_SESSION['OIDetail']->OIEntries as $OIItem) {
	echo "<tr><td ALIGN=RIGHT>" . number_format($OIItem->Amount, 2) . "</td>
		<td>" . $OIItem->OIIDDesc . "</td>
		<td>" . $OIItem->EmployeeID . ' - ' . $OIItem->LastName . ',' . $OIItem->FirstName . "</td>
		<td><a href='" . basename(__FILE__) . '?' . SID . '&Delete=' . $OIItem->ID . "'>" . _('Delete') . '</a></td>
	</tr>';
}

echo '<tr><td ALIGN=RIGHT><B>' . number_format($_SESSION['OIDetail']->OITotal, 2) . '</B></td></tr></table>';

if ((ABS($_SESSION['OIDetail']->OITotal) > 0.001 and $_SESSION['OIDetail']->OIItemCounter > 0) and $_SESSION['OIDetail']->OIItemCounter > 0) {
	echo "<BR><BR><input type=SUBMIT name='CommitBatch' value='" . _('Accept and Process Other Income') . "'>";
}

echo '</form>';
include ('includes/footer.php');
?>