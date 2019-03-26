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
	$SQL = "UPDATE prlpayrolltrans SET	grosspay=0
				WHERE payrollid ='" . $PayrollID . "'";
	$RePostGPay = DB_query($SQL);

	$SQL = "SELECT counterindex,payrollid,employeeid,basicpay,othincome,absent,late,otpay
			FROM prlpayrolltrans
			WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
	$PayDetails = DB_query($SQL);
	if (DB_num_rows($PayDetails) > 0) {
		while ($MyRow = DB_fetch_array($PayDetails)) {
			$GrossPay = $MyRow['basicpay'] + $MyRow['otpay'] + $MyRow['othincome'] - $MyRow['absent'] - $MyRow['late'];
			$SQL = 'UPDATE prlpayrolltrans SET grosspay=' . $GrossPay . '
						WHERE counterindex = ' . $MyRow['counterindex'];
			$PostGPay = DB_query($SQL);
		}
	}
}
?>