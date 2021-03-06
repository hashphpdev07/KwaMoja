<?php
$PageSecurity = 2;

if (isset($_POST['PrintPDF']) and isset($_POST['FSYear'])) {

	include ('config.php');
	include ('includes/PDFStarter.php');
	include ('includes/ConnectDB.php');
	include ('includes/DateFunctions.php');
	include ('includes/prlFunctions.php');

	/* A4_Landscape */

	$Page_Width = 842;
	$Page_Height = 595;
	$Top_Margin = 20;
	$Bottom_Margin = 20;
	$Left_Margin = 25;
	$Right_Margin = 22;

	$PageSize = array(0, 0, $Page_Width, $Page_Height);
	$PDF = new Cpdf($PageSize);

	$PageNumber = 0;

	$PDF->selectFont('./fonts/Helvetica.afm');

	/* Standard PDF file creation header stuff */
	$PDF->addinfo('Title', _('Alphalist'));
	$PDF->addinfo('Subject', _('Alphalist'));

	$PageNumber = 1;
	$line_height = 12;

	$PageNumber = 0;
	$FontSize = 10;
	$PDF->addinfo('Title', _('Alphalist'));
	$PDF->addinfo('Subject', _('Alphalist'));
	$line_height = 12;
	include ('includes/PDFTaxYTDPageHeader.php');
	//list of all employees
	$SQL = "SELECT employeeid
			FROM prlemployeemaster
			WHERE prlemployeemaster.employeeid<>''";
	$EmpListResult = DB_query($SQL, _('Could not test to see that all detail records properly initiated'));
	if (DB_num_rows($EmpListResult) > 0) {
		while ($emprow = DB_fetch_array($EmpListResult)) {
			$k = 0; //row colour counter
			$SQL = "SELECT sum(taxableincome) AS Gross,sum(tax) AS Tax
					FROM prlemptaxfile
					WHERE prlemptaxfile.employeeid='" . $emprow['employeeid'] . "'
					AND prlemptaxfile.fsyear='" . $FSYear . "'";
			$PayResult = DB_query($SQL);
			if (DB_num_rows($PayResult) > 0) {
				$MyRow = DB_fetch_array($PayResult);
				$EmpID = $emprow['employeeid'];
				$TaxNumber = GetEmpRow($EmpID, 23);
				$TaxID = GetEmpRow($EmpID, 35);
				$FullName = GetName($EmpID);
				$MyExemption = GetTaxStatusRow(GetEmpRow($EmpID, $db, 35), $db, 4);
				$Gross = $MyRow['Gross'];
				$NetTaxable = $MyRow['Gross'] - $MyExemption;
				$TaxWithheld = $MyRow['Tax'];
				$MyTax = GetMyTax($NetTaxable);
				$Refund = $MyTax - $TaxWithheld;
				$GTNetTaxable+= $NetTaxable;
				$GTMyTax+= $MyTax;
				$GTTaxWithheld+= $TaxWithheld;
				$GTRefund+= $Refund;

				//$YPos -= (2 * $line_height);  //double spacing
				$FontSize = 8;
				$PDF->selectFont('./fonts/Helvetica.afm');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 120, $FontSize, $FullName, 'left');
				$LeftOvers = $PDF->addTextWrap(150, $YPos, 60, $FontSize, $TaxNumber, 'right');
				$LeftOvers = $PDF->addTextWrap(220, $YPos, 60, $FontSize, $TaxID, 'right');
				$LeftOvers = $PDF->addTextWrap(290, $YPos, 60, $FontSize, number_format($Gross, 2), 'right');
				$LeftOvers = $PDF->addTextWrap(360, $YPos, 60, $FontSize, number_format($MyExemption, 2), 'right');
				$LeftOvers = $PDF->addTextWrap(430, $YPos, 60, $FontSize, number_format($NetTaxable, 2), 'right');
				$LeftOvers = $PDF->addTextWrap(500, $YPos, 60, $FontSize, number_format($MyTax, 2), 'right');
				$LeftOvers = $PDF->addTextWrap(570, $YPos, 60, $FontSize, number_format($TaxWithheld, 2), 'right');
				$LeftOvers = $PDF->addTextWrap(660, $YPos, 60, $FontSize, number_format($Refund, 2), 'right');
				$YPos-= $line_height;
				if ($YPos < ($Bottom_Margin)) {
					include ('includes/PDFTaxYTDPageHeader.php');
				}
			}
		}
	}

	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);
	$YPos-= (2 * $line_height);
	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, 'Grand Total');
	$LeftOvers = $PDF->addTextWrap(500, $YPos, 60, $FontSize, number_format($GTMyTax, 2), 'right');
	$LeftOvers = $PDF->addTextWrap(570, $YPos, 60, $FontSize, number_format($GTTaxWithheld, 2), 'right');
	$LeftOvers = $PDF->addTextWrap(660, $YPos, 60, $FontSize, number_format($GTRefund, 2), 'right');
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);

	$PDFcode = $PDF->output();
	$len = strlen($PDFcode);
	if ($len <= 20) {
		$Title = _('Alphalist error');
		include ('includes/header.php');
		echo '<p>';
		prnMsg(_('There were no entries to print out for the selections specified'));
		echo '<BR><A HREF="' . $RootPath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
		include ('includes/footer.php');
		exit;
	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=Alphalist.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		$PDF->Stream();

	}
	exit;

} elseif (isset($_POST['ShowPR'])) {
	include ('includes/session.php');
	$Title = _('Alphalist');
	include ('includes/header.php');
	echo 'Use PrintPDF instead';
	echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	include ('includes/footer.php');
	exit;
} else { /*The option to print PDF was not hit */
	include ('includes/session.php');
	$Title = _('Alphalist');
	include ('includes/header.php');
	echo "<form method='post' action='" . basename(__FILE__) . '?' . SID . "'>";
	echo '<table>';
	echo '</select></td></tr>';
	echo '<tr><td><align="centert"><b>' . _('FS Year') . ":<select name='FSYear'>";
	echo '<option selected="selected" value=0>' . _('Select One');
	for ($yy = 2006;$yy <= 2015;$yy++) {
		echo "<option value=$yy>$yy</option>\n";
	}
	echo '</select></td></tr>';
	echo '</table><P><input type="submit" name="ShowPR" value="' . _('Show Alpalist') . '">';
	echo '<P><input type="submit" name="PrintPDF" value="' . _('PrintPDF') . '">';
	include ('includes/footer.php');;

} /*end of else not PrintPDF */

?>