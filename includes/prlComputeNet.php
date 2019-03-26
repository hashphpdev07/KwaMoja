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
	$SQL = "UPDATE prlpayrolltrans SET	netpay=0
				WHERE payrollid ='" . $PayrollID . "'";
	$RePostNPay = DB_query($SQL);

	$SQL = "SELECT counterindex,payrollid,employeeid,grosspay,loandeduction,sss,hdmf,philhealth,tax
			FROM prlpayrolltrans
			WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
	$PayDetails = DB_query($SQL);
	if (DB_num_rows($PayDetails) > 0) {
		while ($MyRow = DB_fetch_array($PayDetails)) {
			$NetPay = $MyRow['grosspay'] - $MyRow['loandeduction'] - $MyRow['sss'] - $MyRow['hdmf'] - $MyRow['philhealth'] - $MyRow['tax'];
			$SQL = 'UPDATE prlpayrolltrans SET netpay=' . $NetPay . '
						WHERE counterindex = ' . $MyRow['counterindex'];
			$PostNPay = DB_query($SQL);
		}
	}
	echo "Finished processing payroll...";
}
?>