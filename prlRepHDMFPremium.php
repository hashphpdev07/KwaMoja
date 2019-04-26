<?php
/* $Revision: 1.0 $ */

$PageSecurity = 2;

if (isset($_POST['PrintPDF']) and isset($_POST['FSMonth']) and $_POST['FSMonth'] >= 0 and isset($_POST['FSYear']) and $_POST['FSYear'] >= 0) {

	include ('config.php');
	include ('includes/PDFStarter.php');
	include ('includes/ConnectDB.php');
	include ('includes/DateFunctions.php');
	include ('includes/prlFunctions.php');

	$FontSize = 12;
	$PDF->addinfo('Title', _('HDMF Monthly Premium'));
	$PDF->addinfo('Subject', _('HDMF Monthly Premium'));

	$PageNumber = 0;
	$line_height = 12;

	if ($_POST['FSMonth'] == 0) {
		$Title = _('HDMF Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Month not selected'), 'error');
		echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include ('includes/footer.php');
		exit;
	}
	if ($_POST['FSYear'] == 0) {
		$Title = _('HDMF Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Year not selected'), 'error');
		echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include ('includes/footer.php');
		exit;
	}
	$HDMFMonth = $_POST['FSMonth'];
	$HDMFYear = $_POST['FSYear'];
	$HDMFMonthStr = GetMonthStr($HDMFMonth);
	$PageNumber = 0;
	$FontSize = 10;
	$line_height = 12;
	$FullName = '';
	$HDMFNumber = '';
	$HDMFER = 0;
	$HDMFEC = 0;
	$HDMFEE = 0;
	$HDMFTotal = 0;

	include ('includes/PDFHDMFPremiumPageHeader.php');

	$SQL = "SELECT employeeid,employerhdmf,employeehdmf,total
			FROM prlemphdmffile
			WHERE prlemphdmffile.fsmonth='" . $HDMFMonth . "'
			AND prlemphdmffile.fsyear='" . $HDMFYear . "'";
	$HDMFDetails = DB_query($SQL);
	if (DB_num_rows($HDMFDetails) > 0) {
		//although it is assume that hdmf deduction once only every month but who knows
		while ($hdmfrow = DB_fetch_array($HDMFDetails)) {
			$EmpID = $hdmfrow['employeeid'];
			$FullName = GetName($EmpID);
			$HDMFNumber = GetEmpRow($EmpID, 21);
			$HDMFER = $hdmfrow['employerhdmf'];
			$HDMFEE = $hdmfrow['employeehdmf'];
			$HDMFTotal = $hdmfrow['total'];
			$GTHDMFER+= $HDMFER;
			$GTHDMFEE+= $HDMFEE;
			$GTHDMFTotal+= $HDMFTotal;
			//$YPos -= (2 * $line_height);  //double spacing
			if ($HDMFTotal > 0) {
				$FontSize = 8;
				$PDF->selectFont('./fonts/Helvetica.afm');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, $FullName);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 200, $YPos, 50, $FontSize, $HDMFNumber, 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($HDMFER, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($HDMFEE, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($HDMFTotal, 2), 'right');
				$YPos-= $line_height;
				if ($YPos < ($Bottom_Margin)) {
					include ('includes/PDFHDMFPremiumPageHeader.php');
				}
			}
		}
	}
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);
	$YPos-= (2 * $line_height);
	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, 'Grand Total');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($GTHDMFER, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($GTHDMFEE, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($GTHDMFTotal, 2), 'right');
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);

	$buf = $PDF->output();
	$len = strlen($buf);

	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=HDMFListing.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$PDF->stream();

} elseif (isset($_POST['ShowPR'])) {
	include ('includes/session.php');
	$Title = _('HDMF Monthly Premium Listing');
	include ('includes/header.php');
	echo 'Use PrintPDF instead';
	echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	include ('includes/footer.php');
	exit;
} else { /*The option to print PDF was not hit */

	include ('includes/session.php');
	$Title = _('HDMF Monthly Premium Listing');
	include ('includes/header.php');

	echo "<form method='post' action='" . basename(__FILE__) . '?' . SID . "'>";
	echo '<table>';
	echo '</select></td></tr>';
	echo '<tr><td><align="centert"><b>' . _('FS Month') . ":<select name='FSMonth'>";
	echo '<option selected="selected" value=0>' . _('Select One');
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
	echo '</select>';
	echo '<select name="FSYear">';
	echo '<option selected="selected" value=0>' . _('Select One');
	for ($yy = 2006;$yy <= 2015;$yy++) {
		echo "<option value=$yy>$yy</option>\n";
	}
	echo '</select></td></tr>';

	echo '</table><P><input type="submit" name="ShowPR" value="' . _('Show HDMF Premium') . '">';
	echo '<P><input type="submit" name="PrintPDF" value="' . _('PrintPDF') . '">';

	include ('includes/footer.php');;
} /*end of else not PrintPDF */

?>