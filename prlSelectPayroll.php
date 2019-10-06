<?php
/* $Revision: 1.0 $ */

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('Payroll Master Maintenance');

include ('includes/header.php');

echo "<table WIDTH=30% BORDER=2><tr></tr>";
echo '<tr><td WIDTH=100%>';
echo '<a href="' . $RootPath . '/prlEditPayroll.php?SelectedAccountr=' . $_SESSION[''] . '">' . _('Create Payroll Period') . '</a><BR>';
echo '</td><td WIDTH=100%>';
echo '</td></tr></table><BR>';

if (isset($_GET['PayrollID'])) {
	$PayrollID = $_GET['PayrollID'];
} elseif (isset($_POST['PayrollID'])) {
	$PayrollID = $_POST['PayrollID'];
}

if (isset($_GET['delete'])) {
	//the link to delete a selected record was clicked instead of the submit button
	$SQL = "DELETE FROM prlemployeemaster WHERE employeeid " . LIKE . "'" . DB_escape_string($SelectedEmployeeID) . "'";
	$Result = DB_query($SQL);
	prnMsg('employee id has been deleted' . '!', 'success');
	//}
	//end if account group used in GL accounts
	unset($PayrollID);
	unset($_GET['PayrollID']);
	unset($_GET['select']);
	unset($_POST['PayrollID']);

}

if (!isset($PayrollID)) {
	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedAccount will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true and the list of ChartMaster will be displayed with
	links to delete or edit each. These will call the same page again and allow update/input
	or deletion of the records*/

	$SQL = "SELECT payrollid,
            payrolldesc,
			fsmonth,
			fsyear,
			startdate,
			enddate,
			payperiodid
		FROM prlpayrollperiod
		ORDER BY payrollid";
	$ErrMsg = _('The payroll record could not be retrieved because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<th>" . _('Payroll ID') . "</th>
		<th>" . _('Desciption') . "</th>
		<th>" . _('FS Month') . "</th>
		<th>" . _('FS Year') . "</th>
		<th>" . _('Start Date') . "</th>
		<th>" . _('End Date') . "</th>
		<th>" . _('Pay Period ') . "</th>
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
		echo '<td><A HREF="' . $RootPath . '/prlCreatePayroll.php?' . SID . '&PayrollID=' . $MyRow[0] . '">' . _('Select') . '</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	//END WHILE LIST LOOP
	
} //END IF SELECTED ACCOUNT


echo '</table>';
//end of ifs and buts!
include ('includes/footer.php');
?>