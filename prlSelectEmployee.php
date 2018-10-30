<?php
/* $Revision: 1.0 $ */

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('Emloyee Master Record Maintenance');

include ('includes/header.php');

if (isset($_GET['EmployeeID'])) {
	$EmployeeID = $_GET['EmployeeID'];
} elseif (isset($_POST['EmployeeID'])) {
	$EmployeeID = $_POST['EmployeeID'];
}

echo '<p class="page_title_text">
		<img class="page_title_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', $Title, '" alt="', $Title, '" />', $Title, '
	</p>';

if (isset($_GET['delete'])) {
	//the link to delete a selected record was clicked instead of the submit button
	// Get the original name of the marital status the ID is just a secure way to find the marital status
	$SQL = "SELECT employeemaster.employeeid FROM employeemaster
		WHERE employeemaster.employeeid = " . DB_escape_string($SelectedEmployeeID);
	$Result = DB_query($SQL);
	if (DB_num_rows($Result) == 0) {
		// This is probably the safest way there is
		prnMsg(_('Cannot delete this employee record because it no longer exist'), 'warn');
	} else {
		$MyRow = DB_fetch_row($Result);
		$OldEmployeeID = $MyRow[0];
		$SQL = "DELETE FROM prlemployeemaster WHERE employeeid " . LIKE . "'" . DB_escape_string($SelectedEmployeeID) . "'";
		$Result = DB_query($SQL);
		prnMsg('member id has been deleted' . '!', 'success');
	}
	//end if account group used in GL accounts
	unset($EmployeeID);
	unset($_GET['EmployeeID']);
	unset($_GET['delete']);
	unset($_POST['EmployeeID']);
}

if (!isset($EmployeeID)) {
	$SQL = "SELECT prlemployeemaster.employeeid,
					prlemployeemaster.lastname,
					prlemployeemaster.firstname,
					prlemployeemaster.payperiodid,
					prlemployeemaster.paytype,
					prlemployeemaster.marital,
					prlemployeemaster.periodrate,
					prlemployeemaster.birthdate,
					prlemployeemaster.active,
					prlemployeemaster.payperiodid,
					prlpayperiod.payperiodid,
					prlpayperiod.payperioddesc
				FROM prlemployeemaster
				INNER JOIN prlpayperiod
					ON prlemployeemaster.payperiodid=prlpayperiod.payperiodid
				ORDER BY lastname,
						firstname";

	$ErrMsg = _('The employee master record could not be retrieved because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table>
			<tr>
				<th>', _('Employee ID'), '</th>
				<th>', _('Last Name '), '</th>
				<th>', _('First Name'), '</th>
				<th>', _('Pay Type  '), '</th>
				<th>', _('Marital Status'), '</th>
				<th>', _('Basic Pay '), '</th>
				<th>', _('Date of Birth'), '</th>
				<th>', _('Active   '), '</th>
				<th>', _('PayPeriod'), '</th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr class="striped_row">
				<td>', $MyRow['employeeid'], '</td>
				<td>', $MyRow['lastname'], '</td>
				<td>', $MyRow['firstname'], '</td>
				<td>', $MyRow['paytype'], '</td>
				<td>', $MyRow['marital'], '</td>
				<td>', $MyRow['periodrate'], '</td>
				<td>', ConvertSQLDate($MyRow['birthdate']), '</td>
				<td>', $MyRow['active'], '</td>
				<td>', $MyRow['payperioddesc'], '</td>
				<td><a href="', $RootPath, '/prlEmployeeMaster.php?EmployeeID=', urlencode($MyRow['employeeid']), '">', _('Edit/Delete'), '</a></td>
			</tr>';
	} //END WHILE LIST LOOP
	
}

echo '</table>';

echo '<div class="centre">
		<a href="', $RootPath, '/prlEmployeeMaster.php">', _('Add Employee records'), '</a>
	</div>';

include ('includes/footer.php');
?>