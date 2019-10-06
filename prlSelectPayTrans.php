<?php
/* $Revision: 1.0 $ */

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('View Payroll Data');

include ('includes/header.php');

if (isset($_GET['Counter'])) {
	$Counter = $_GET['Counter'];
} elseif (isset($_POST['Counter'])) {
	$Counter = $_POST['Counter'];
} else {
	unset($Counter);
}

/*
if (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

	$SQL = "SELECT payrollid
				FROM prlpayrollperiod
				WHERE prlpayrollperiod.payrollid='" . $PayrollID . "'
				AND prlpayrollperiod.payclosed='1'";
		$PayDetails = DB_query($SQL);
		if(DB_num_rows($PayDetails)>0)
		{
		  $CancelDelete = 1;
		  prnMsg('Payroll is closed. Can not delete this record...','success');
		}


// PREVENT DELETES IF DEPENDENT RECORDSs
	if ($CancelDelete == 0) {
		$SQL="DELETE FROM prlpayrolltrans WHERE counterindex='$Counter'";
		$Result = DB_query($SQL);
		prnMsg(_('Payroll record ') . ' ' . $Counter . ' ' . _('has been deleted'),'success');
		unset($Counter);
		unset($_SESSION['Counter']);
	} //end of Delete
}
*/

if (!isset($Counter)) {
	echo "<form method='post' ACTION='" . basename(__FILE__) . "?" . SID . "'>";
	echo "<input type='hidden' name='New' value='Yes'>";
	echo '<table>';

	$SQL = "SELECT  	payrollid,
						employeeid,
						periodrate,
						hourlyrate,
						basicpay,
						othincome,
						absent,
						late,
						otpay,
						grosspay,
						loandeduction,
						sss,
						hdmf,
						philhealth,
						tax,
						netpay,
						fsmonth,
						fsyear
		FROM prlpayrolltrans
		ORDER BY counterindex";
	$ErrMsg = _('Payroll record could not be retrieved because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<th>" . _('Pay ID ') . "</th>
		<th>" . _('Emp ID') . "</th>
		<th>" . _('Period Rate') . "</th>
		<th>" . _('Hourly Rate') . "</th>
		<th>" . _('Basic Pay') . "</th>
		<th>" . _('Other Income') . "</th>
		<th>" . _('Absent') . "</th>
		<th>" . _('Late') . "</th>
		<th>" . _('Overtime Pay') . "</th>
		<th>" . _('Gross Pay') . "</th>
		<th>" . _('Loan Deduction') . "</th>
		<th>" . _('SSS') . "</th>
		<th>" . _('HDMF') . "</th>
		<th>" . _('PhilHealth') . "</th>
		<th>" . _('Tax') . "</th>
		<th>" . _('Net Pay') . "</th>
		<th>" . _('Month') . "</th>
		<th>" . _('Year') . "</th>
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
		echo '<td>' . $MyRow[7] . '</td>';
		echo '<td>' . $MyRow[8] . '</td>';
		echo '<td>' . $MyRow[9] . '</td>';
		echo '<td>' . $MyRow[10] . '</td>';
		echo '<td>' . $MyRow[11] . '</td>';
		echo '<td>' . $MyRow[12] . '</td>';
		echo '<td>' . $MyRow[13] . '</td>';
		echo '<td>' . $MyRow[14] . '</td>';
		echo '<td>' . $MyRow[15] . '</td>';
		echo '<td>' . $MyRow[16] . '</td>';
		echo '<td>' . $MyRow[17] . '</td>';
		//echo '<td><A HREF="' . basename(__FILE__) . '?' . SID . '&Counter=' . $MyRow[0] . '&delete=1">' . _('Delete') .'</A></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	//END WHILE LIST LOOP
	
} //END IF SELECTED ACCOUNT


echo '</table>';
//end of ifs and buts!
include ('includes/footer.php');
?>