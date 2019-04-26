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
	$FromPeriod = GetPayrollRow($PayrollID, 3);
	$ToPeriod = GetPayrollRow($PayrollID, 4);
	$SQL = "UPDATE prldailytrans SET	absentamt=0,lateamt=0
				WHERE prldailytrans.payrollid ='" . $PayrollID . "'";
	$RePostAbs = DB_query($SQL);

	$SQL = "SELECT counterindex,payrollid,employeeid,hourlyrate
			FROM prlpayrolltrans
			WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
	$PayDetails = DB_query($SQL);
	if (DB_num_rows($PayDetails) > 0) {
		while ($MyRow = DB_fetch_array($PayDetails)) {
			$SQL = "SELECT counterindex,payrollid,employeeid,absenthrs,latehrs,absentamt,lateamt
					FROM prldailytrans
			        WHERE prldailytrans.employeeid='" . $MyRow['employeeid'] . "'
					AND rtdate>='$FromPeriod'
					AND  rtdate<='$ToPeriod'
					ORDER BY RTDate";
			$RTDetails = DB_query($SQL);
			if (DB_num_rows($RTDetails) > 0) {
				while ($rtrow = DB_fetch_array($RTDetails)) {
					if (($rtrow['payrollid'] == $PayrollID) or ($rtrow['payrollid'] == '')) {
						$HRRate = $MyRow['hourlyrate'];
						$SQL = 'UPDATE prldailytrans SET payrollid=' . $PayrollID . ',absentamt=absenthrs*' . $HRRate . ',lateamt=latehrs*' . $HRRate . '
											WHERE counterindex = ' . $rtrow['counterindex'];
						$PostBPay = DB_query($SQL);
					}
				}
			}
		}
		//         exit("Found...");
		
	} else {
		//exit("No Found...");
		
	}

	$SQL = "SELECT counterindex,payrollid,employeeid,periodrate,hourlyrate,absent,late
			FROM prlpayrolltrans
			WHERE prlpayrolltrans.payrollid='" . $PayrollID . "'";
	$PayDetails = DB_query($SQL);
	if (DB_num_rows($PayDetails) > 0) {
		while ($MyRow = DB_fetch_array($PayDetails)) {
			$PayType = GetPayTypeDesc(GetEmpRow($MyRow['employeeid'], 29));
			//no late or absent for an hourly employees beacuse they are based on hours worked
			if ($PayType == 'Salary') {
				$SQL = "SELECT sum(lateamt) AS Late, sum(absentamt) AS Absent
					FROM prldailytrans
					WHERE prldailytrans.employeeid='" . $MyRow['employeeid'] . "'
					AND payrollid='" . $MyRow['payrollid'] . "'
					ORDER BY RTDate";
				$RTDetails = DB_query($SQL);
				if (DB_num_rows($RTDetails) > 0) {
					$bprow = DB_fetch_array($RTDetails);
					$LateDed = $bprow['Late'];
					$AbsentDed = $bprow['Absent'];
					if (($LateDed > 0) or ($AbsentDed > 0)) {
						$SQL = 'UPDATE prlpayrolltrans SET absent=' . $AbsentDed . ', late=' . $LateDed . '
								WHERE prlpayrolltrans.counterindex = ' . $MyRow['counterindex'];
						$PostRTPay = DB_query($SQL);
					}
				}
			}
		}
	}
}
?>