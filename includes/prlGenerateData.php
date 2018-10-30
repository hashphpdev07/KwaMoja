<?php
if (isset($_GET['PayrollID'])) {
	$PayrollID = $_GET['PayrollID'];
} elseif (isset($_POST['PayrollID'])) {
	$PayrollID = $_POST['PayrollID'];
} else {
	unset($PayrollID);
}
$Status = GetOpenCloseStr(GetPayrollRow($PayrollID, 11));
if ($Status == 'Closed') {
	exit("Payroll is Closed. Re-open first...");
}
if (isset($_POST['submit'])) {
	exit("Contact Administrator...");
} else {
	$SQL = "DELETE FROM prlpayrolltrans WHERE payrollid ='" . $PayrollID . "'";
	$Postdelptrans = DB_query($SQL);
	$PayPeriodID = GetPayrollRow($PayrollID, 2);
	$FSMonthRow = GetPayrollRow($PayrollID, 5);
	$FSYearRow = GetPayrollRow($PayrollID, 6);
	$SQL = 'SELECT employeeid,periodrate,hourlyrate
			FROM prlemployeemaster
			WHERE prlemployeemaster.payperiodid = ' . $PayPeriodID . ' and prlemployeemaster.active=0';
	$ChartDetailsNotSetUpResult = DB_query($SQL, _('Could not test to see that all detail records properly initiated'));
	if (DB_num_rows($ChartDetailsNotSetUpResult) > 0) {
		$SQL = 'INSERT INTO prlpayrolltrans(employeeid,periodrate,hourlyrate)
				SELECT employeeid,periodrate,hourlyrate
				FROM prlemployeemaster
				WHERE prlemployeemaster.payperiodid = ' . $PayPeriodID . ' and prlemployeemaster.active=0';
		$ErrMsg = _('Inserting new chart details records required failed because');
		$InsChartDetailsRecords = DB_query($SQL, $ErrMsg);
		$SQL = "UPDATE prlpayrolltrans SET
		          payrollid='" . $PayrollID . "'
			WHERE payrollid = ''";
		$PostPrd = DB_query($SQL);

		$SQL = "UPDATE prlpayrolltrans SET fsmonth=$FSMonthRow, fsyear=$FSYearRow
				WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
		$PostFSPeriod = DB_query($SQL);
	} else {
		exit("No Employees Records Match....");
	}

}

?>