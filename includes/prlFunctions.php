<?php
//include_once("ConnectDB_mysql.inc");
// All Functions
// *************
//function beg() /
//{
//var myConfirm = confirm("Sure you want to leave?");
//return myConfirm
//}


//
function GetYesNoStr($YesNo) {
	if ($YesNo == 0) {
		$YesNoStr = 'Yes';
	} else {
		$YesNoStr = 'No';
	}
	return $YesNoStr;
}

function GetOpenCloseStr($OC) {
	if ($OC == 0) {
		$OCStr = 'Open';
	} else {
		$OCStr = 'Closed';
	}
	return $OCStr;
}

function GetPayTypeDesc($PT) {
	if ($PT == 0) {
		$PTStr = 'Salary';
	} elseif ($PT == 1) {
		$PTStr = 'Hourly';
	} else {
		$PTStr = 'Unknown';
	}
	return $PTStr;
}

function GetPayPeriodDesc($PeriodID) {

	/*Gets the GL Codes relevant to the stock item account from the stock category record */

	$QuerySQL = "SELECT payperiodid, payperioddesc FROM prlpayperiod
				 WHERE payperiodid = '$PeriodID'";
	$ErrMsg = _('The period code could not be retreived because');
	$GetPayDescResult = DB_query($QuerySQL, $ErrMsg);

	$MyRow = DB_fetch_array($GetPayDescResult);
	return $MyRow[1];
}

function GetOthIncRow($OIID, $PayRow) {

	/*Gets the GL Codes relevant to the stock item account from the stock category record */
	$SQL = "SELECT othincdesc,taxable FROM prlothinctable
				 WHERE othincid = '$OIID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow[$PayRow];;
}

function GetPayPeriodRow($PeriodID, $PayRow) {

	/*Gets the GL Codes relevant to the stock item account from the stock category record */
	$SQL = "SELECT payperiodid, payperioddesc,numberofpayday FROM prlpayperiod
				 WHERE payperiodid = '$PeriodID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow[$PayRow];;
}

function GetMyTax($MyTaxableIncome) {
	if ($MyTaxableIncome > 0) {
		$SQL = "SELECT rangefrom,rangeto,fixtaxableamount,fixtax,percentofexcessamount
				FROM prltaxtablerate
				WHERE rangefrom<='$MyTaxableIncome'
				AND rangeto>='$MyTaxableIncome'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$MyFixTax = $MyRow['fixtax'];
		$MyTaxAmt = $MyFixTax + (($MyTaxableIncome - $MyRow['fixtaxableamount']) * ($MyRow['percentofexcessamount'] / 100));
	} else {
		$MyTaxAmt = 0;
	}
	return $MyTaxAmt;
}

function GetMyTax2($MyEstTaxableIncome, $FSPPID, $MyTaxStatusID) {
	if ($MyEstTaxableIncome > 0) {
		$SQL = "SELECT payperiodid,taxstatusid,rangefrom,rangeto,fixtaxableamount,fixtax,percentofexcessamount
				FROM prltaxtablerate2
				WHERE rangefrom<='$MyEstTaxableIncome'
				AND rangeto>='$MyEstTaxableIncome'
				AND payperiodid='$FSPPID'
				AND taxstatusid='$MyTaxStatusID'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$MyFixTax = $MyRow['fixtax'];
		$MyTaxAmt = $MyFixTax + (($MyEstTaxableIncome - $MyRow['fixtaxableamount']) * ($MyRow['percentofexcessamount'] / 100));
	} else {
		$MyTaxAmt = 0;
	}
	return $MyTaxAmt;
}

function GetHDMFEE($GrossIncome) {
	$SQL = "SELECT rangefrom,rangeto,dedtypeee,employeeshare
			FROM prlhdmftable
			WHERE rangefrom<='$GrossIncome'
			AND rangeto>='$GrossIncome'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	if ($MyRow['dedtypeee'] == 'Fixed') {
		$MyHDMFAmt = $MyRow['employeeshare'];
	} elseif ($MyRow['dedtypeee'] == 'Percentage') {
		$MyHDMFAmt = $GrossIncome * ($MyRow['employeeshare'] / 100);
	} else {
		$MyHDMFAmt = 0;
	}
	return $MyHDMFAmt;
}

function GetHDMFER($GrossIncome) {
	$SQL = "SELECT rangefrom,rangeto,dedtypeer,employershare
			FROM prlhdmftable
			WHERE rangefrom<='$GrossIncome'
			AND rangeto>='$GrossIncome'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	if ($MyRow['dedtypeer'] == 'Fixed') {
		$MyHDMFAmt = $MyRow['employeeshare'];
	} elseif ($MyRow['dedtypeer'] == 'Percentage') {
		$MyHDMFAmt = $GrossIncome * ($MyRow['employershare'] / 100);
	} else {
		$MyHDMFAmt = 0;
	}
	return $MyHDMFAmt;
}

function GetTaxStatusRow($TaxID, $PayRow) {
	$SQL = "SELECT taxstatusid,taxstatusdescription,personalexemption,additionalexemption,totalexemption
			FROM prltaxstatus
			WHERE taxstatusid='$TaxID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow[$PayRow];
}

function GetPayrollRow($PayrollID, $PayRow) {
	//payrollid - 0, and so on
	/*Gets the GL Codes relevant to the stock item account from the stock category record */
	//$SQL = "SELECT payrollidyrolldesc,payperiodid,startdate,enddate,fsmonth,fsyear,payclosed
	$SQL = "SELECT payrollid,payrolldesc,payperiodid,startdate,enddate,fsmonth,fsyear,deductsss,deducthdmf,deductphilhealth,payclosed
			FROM prlpayrollperiod
			WHERE payrollid = '$PayrollID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	if ($PayRow == 11) return $MyRow['payclosed'];
	return $MyRow[$PayRow];
}

function GetEmpRow($EmpID, $EmpRow) {
	$SQL = "SELECT paytype,payperiodid,periodrate,hourlyrate,marital,taxstatusid,employmentid,active,ssnumber,hdmfnumber,phnumber,taxactnumber,atmnumber
			FROM prlemployeemaster
			WHERE employeeid= '$EmpID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	if ($EmpRow == 35) return $MyRow['taxstatusid'];
	if ($EmpRow == 29) return $MyRow['paytype'];
	if ($EmpRow == 19) return $MyRow['atmnumber'];
	if ($EmpRow == 20) return $MyRow['ssnumber'];
	if ($EmpRow == 21) return $MyRow['hdmfnumber'];
	if ($EmpRow == 22) return $MyRow['phnumber'];
	if ($EmpRow == 23) return $MyRow['taxactnumber'];
	return $MyRow[$PayRow];
}

function GetName($EmpID) {
	$SQL = "SELECT lastname,firstname,middlename
			FROM prlemployeemaster
			WHERE employeeid= '$EmpID'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow['lastname'] . ', ' . $MyRow['firstname'] . ', ' . $MyRow['middlename'];
}

function GetSSSRow($SSSGross) {
	$SQL = "SELECT rangefrom,rangeto,salarycredit,employerss,employerec,employeess,total
			FROM prlsstable
			WHERE rangefrom<='$SSSGross'
			AND rangeto>='$SSSGross'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow;
}

function GetPHRow($PHGross) {
	$SQL = "SELECT rangefrom,rangeto,salarycredit,employerph,employerec,employeeph,total
			FROM prlphilhealth
			WHERE rangefrom<='$PHGross'
			AND rangeto>='$PHGross'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	return $MyRow;
}

function GetMonthStr($Mos) {
	if ($Mos == 1) {
		$MosStr = 'January';
	} elseif ($Mos == 2) {
		$MosStr = 'February';
	} elseif ($Mos == 3) {
		$MosStr = 'March';
	} elseif ($Mos == 4) {
		$MosStr = 'April';
	} elseif ($Mos == 5) {
		$MosStr = 'May';
	} elseif ($Mos == 6) {
		$MosStr = 'June';
	} elseif ($Mos == 7) {
		$MosStr = 'July';
	} elseif ($Mos == 8) {
		$MosStr = 'August';
	} elseif ($Mos == 9) {
		$MosStr = 'September';
	} elseif ($Mos == 10) {
		$MosStr = 'October';
	} elseif ($Mos == 11) {
		$MosStr = 'November';
	} elseif ($Mos == 12) {
		$MosStr = 'December';
	} else {
		$MosStr = 'Month';
	}
	return $MosStr;
}

//unused
function monthoption($Mos) {
	$MosStr = GetMonthStr($Mos);
	echo '<option selected="selected" value=$Mos>' . _($MosStr);
	echo '<option value=1>' . _('January');
	echo '<option value=2>' . _('February');
	echo '<option value=3>' . _('March');
	echo '<option value=4>' . _('April');
	echo '<option value=5>' . _('May');
	echo '<option value=6>' . _('June');
	echo '<option value=7>' . _('July');
	echo '<option value=8>' . _('August');
	echo '<option value=9>' . _('September');
	echo '<option value=10>' . _('October');
	echo '<option value=11>' . _('November');
	echo '<option value=12>' . _('December');
	return 1;
}

//unsed
function yearoption($FSYear) {
	if (($FSYear == 0) or ($FSYear == null)) {
		echo '<option selected="selected" value=0>' . _('Year');
	} else {
		echo '<option selected="selected" value=$FSYear>' . _($FSYear);
	}
	for ($yy = 2006;$yy <= 2015;$yy++) {
		echo "<option value=$yy>$yy</option>\n";

	}

	return 1;
}

//unused
function makedropdown($optionid, $optionname, $table) {
	// Query to choose all departments
	$querydrop = "select $optionid,$optionname from $table order by $optionname";
	$Resultdrop = MYSQL_QUERY($querydrop);
	$numberdrop = MYSQL_NUMROWS($Resultdrop);

	if ($numberdrop == 0) {

		echo "<option value=\"\" selected>No Data</option>";

	} else if ($numberdrop > 0) {

		$i = 0;

		echo "<option value=\"\">Please Choose</option>";

		while ($i < $numberdrop) {

			$opid = mysql_result($Resultdrop, $i, "$optionid");
			$opname = mysql_result($Resultdrop, $i, "$optionname");

			echo "<option value=\"$opid\">$opname</option>\n";

			$i++;

		}

	}

	return 0;
}

?>