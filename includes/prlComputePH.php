<?php
if (isset($_GET['PayrollID'])) {
	$PayrollID = $_GET['PayrollID'];
} elseif (isset($_POST['PayrollID'])) {
	$PayrollID = $_POST['PayrollID'];
} else {
	unset($PayrollID);
}
$HowFrequent = 1; //1 -> every payday 2 -> once a month
$FSMonthRow = GetPayrollRow($PayrollID, 5);
$FSYearRow = GetPayrollRow($PayrollID, 6);
$DeductPH = GetYesNoStr(GetPayrollRow($PayrollID, 9));
$Status = GetOpenCloseStr(GetPayrollRow($PayrollID, 11));
if ($Status == 'Closed') {
	exit("Payroll is Closed. Re-open first...");
}
if (isset($_POST['submit'])) {
	exit("Contact Administrator...");
} else {
	$SQL = "DELETE FROM prlempphfile WHERE payrollid ='" . $PayrollID . "'";
	$Postdelph = DB_query($SQL);

	$SQL = "UPDATE prlpayrolltrans SET	philhealth=0
				WHERE payrollid ='" . $PayrollID . "'";
	$RePostPH = DB_query($SQL);

	if ($DeductPH == 'Yes') {
		$SQL = "SELECT counterindex,payrollid,employeeid,basicpay,othincome,absent,late,otpay,grosspay,fsmonth,fsyear
				FROM prlpayrolltrans
				WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
		$PayDetails = DB_query($SQL);
		if (DB_num_rows($PayDetails) > 0) {
			if ($HowFrequent == 2) {
				while ($MyRow = DB_fetch_array($PayDetails)) {
					$SQL = "SELECT sum(basicpay) AS Gross
					FROM prlpayrolltrans
					WHERE prlpayrolltrans.employeeid='" . $MyRow['employeeid'] . "'
					AND prlpayrolltrans.fsmonth='" . $FSMonthRow . "'
					AND prlpayrolltrans.fsyear='" . $FSYearRow . "'";
					$PHDetails = DB_query($SQL);
					if (DB_num_rows($PHDetails) > 0) {
						$phrow = DB_fetch_array($PHDetails);
						$PHGP = $phrow['Gross'];
						if ($PHGP > 30000) {
							$PHGP = 30000;
						}
						if ($PHGP > 0 or $PHGP <> null) {
							$myphrow = GetPHRow($PHGP);
							$SQL = "INSERT INTO prlempphfile (
												payrollid,
												employeeid,
												grosspay,
												rangefrom,
												rangeto,
												salarycredit,
												employerph,
												employerec,
												employeeph,
												total,
												fsmonth,
												fsyear)
												VALUES ('$PayrollID',
													'" . $MyRow['employeeid'] . "',
													'$PHGP',
													'" . $myphrow['rangefrom'] . "',
													'" . $myphrow['rangeto'] . "',
													'" . $myphrow['salarycredit'] . "',
													'" . $myphrow['employerph'] . "',
													'" . $myphrow['employerec'] . "',
													'" . $myphrow['employeeph'] . "',
													'" . $myphrow['total'] . "',
													'" . $MyRow['fsmonth'] . "',
													'" . $MyRow['fsyear'] . "'
													)";
							$ErrMsg = _('Inserting PhilHealth File failed.');
							$InsPHRecords = DB_query($SQL, $ErrMsg);
						} //if sssgp>0
						
					} //dbnumross sssdetials>0
					
				} //end of while
				
			} else {
				while ($MyRow = DB_fetch_array($PayDetails)) {
					$PHGP = $MyRow['basicpay'];
					if ($PHGP > 15000) {
						$PHGP = 15000;
					}
					if ($PHGP > 0 or $PHGP <> null) {
						$myphrow = GetPHRow($PHGP);
						$SQL = "INSERT INTO prlempphfile (
												payrollid,
												employeeid,
												grosspay,
												rangefrom,
												rangeto,
												salarycredit,
												employerph,
												employerec,
												employeeph,
												total,
												fsmonth,
												fsyear)
												VALUES ('$PayrollID',
													'" . $MyRow['employeeid'] . "',
													'$PHGP',
													'" . $myphrow['rangefrom'] . "',
													'" . $myphrow['rangeto'] . "',
													'" . $myphrow['salarycredit'] . "',
													'" . $myphrow['employerph'] . "',
													'" . $myphrow['employerec'] . "',
													'" . $myphrow['employeeph'] . "',
													'" . $myphrow['total'] . "',
													'" . $MyRow['fsmonth'] . "',
													'" . $MyRow['fsyear'] . "'
													)";
						$ErrMsg = _('Inserting PhilHealth File failed.');
						$InsPHRecords = DB_query($SQL, $ErrMsg);
					} //if sssgp>0
					
				} //end of while
				
			} //end of if ($HowFrequent==2) {
			
		} //dbnumrows paydetails > 0
		
	} //deduct sss=yes
	//posting to payroll trans for sss
	if ($DeductPH == 'Yes') {
		$SQL = "SELECT counterindex,payrollid,employeeid,otpay,fsmonth,fsyear
				FROM prlpayrolltrans
				WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
		$PayDetails = DB_query($SQL);
		if (DB_num_rows($PayDetails) > 0) {
			while ($MyRow = DB_fetch_array($PayDetails)) {
				$SQL = "SELECT employeeph
					FROM prlempphfile
			        WHERE prlempphfile.employeeid='" . $MyRow['employeeid'] . "'
					AND prlempphfile.payrollid='" . $PayrollID . "'";
				$PHDetails = DB_query($SQL);
				if (DB_num_rows($PHDetails) > 0) {
					$phrow = DB_fetch_array($PHDetails);
					$PHPayment = $phrow['employeeph'];
					$SQL = 'UPDATE prlpayrolltrans SET philhealth=' . $PHPayment . '
					     WHERE counterindex = ' . $MyRow['counterindex'];
					$PostPHPay = DB_query($SQL);
				}
			}
		}
	}
} //isset post submit

?>
