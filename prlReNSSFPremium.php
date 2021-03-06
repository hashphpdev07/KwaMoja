<?php
if (isset($_POST['PrintPDF']) and isset($_POST['FSMonth']) and $_POST['FSMonth'] >= 0 and isset($_POST['FSYear']) and $_POST['FSYear'] >= 0) {

	include ('config.php');
	include ('includes/PDFStarter.php');
	include ('includes/ConnectDB.php');
	include ('includes/DateFunctions.php');
	include ('includes/prlFunctions.php');

	$FontSize = 12;
	$PDF->addinfo('Title', _('SSS Monthly Premium'));
	$PDF->addinfo('Subject', _('SSS Monthly Premium'));

	$PageNumber = 0;
	$line_height = 12;

	if ($_POST['FSMonth'] == 0) {
		$Title = _('SSS Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Month not selected'), 'error');
		echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include ('includes/footer.php');
		exit;
	}
	if ($_POST['FSYear'] == 0) {
		$Title = _('SSS Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Year not selected'), 'error');
		echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include ('includes/footer.php');
		exit;
	}
	$SSSMonth = $_POST['FSMonth'];
	$SSSYear = $_POST['FSYear'];
	$SSSMonthStr = GetMonthStr($SSSMonth);
	$PageNumber = 0;
	$FontSize = 10;
	$line_height = 12;
	$FullName = '';
	$SSSNumber = '';
	$SSSER = 0;
	$SSSEC = 0;
	$SSSEE = 0;
	$SSSTotal = 0;

	include ('includes/PDFSSSPremiumPageHeader.php');

	$SQL = "SELECT employeeid,employerss,employerec,employeess,total
			FROM prlempnssffile
			WHERE prlempnssffile.fsmonth='" . $SSSMonth . "'
			AND prlempnssffile.fsyear='" . $SSSYear . "'";
	$SSSDetails = DB_query($SQL);
	if (DB_num_rows($SSSDetails) > 0) {
		//although it is assume that sss deduction once only every month but who knows
		while ($sssrow = DB_fetch_array($SSSDetails)) {
			$EmpID = $sssrow['employeeid'];
			$FullName = GetName($EmpID);
			$SSSNumber = GetEmpRow($EmpID, 20);
			$SSSER = $sssrow['employerss'];
			$SSSER = $sssrow['employerss'];
			$SSSEC = $sssrow['employerec'];
			$SSSEE = $sssrow['employeess'];
			$SSSTotal = $sssrow['total'];
			$GTSSSER+= $SSSER;
			$GTSSSEC+= $SSSEC;
			$GTSSSEE+= $SSSEE;
			$GTSSSTotal+= $SSSTotal;
			//$YPos -= (2 * $line_height);  //double spacing
			if ($SSSTotal > 0) {
				$FontSize = 8;
				$PDF->selectFont('./fonts/Helvetica.afm');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, $FullName);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 200, $YPos, 50, $FontSize, $SSSNumber, 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 290, $YPos, 50, $FontSize, number_format($SSSER, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($SSSEC, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($SSSEE, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($SSSTotal, 2), 'right');
				$YPos-= $line_height;
				if ($YPos < ($Bottom_Margin)) {
					include ('includes/PDFSSSPremiumPageHeader.php');
				}
			}
		}
	}
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);
	$YPos-= (2 * $line_height);
	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, 'Grand Total');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 290, $YPos, 50, $FontSize, number_format($GTSSSER, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($GTSSSEC, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($GTSSSEE, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($GTSSSTotal, 2), 'right');
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);

	$buf = $PDF->output();
	$len = strlen($buf);

	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=SSSListing.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$PDF->stream();

} elseif (isset($_POST['ShowPR'])) {
	include ('includes/session.php');
	$Title = _('SSS Monthly Premium Listing');
	include ('includes/header.php');
	echo 'Use PrintPDF instead';
	echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
	include ('includes/footer.php');
	exit;
} else {
	/*The option to print PDF was not hit */

	include ('includes/session.php');
	$Title = _('SSS Monthly Premium Listing');
	include ('includes/header.php');

	echo '<form method="post" action="' . basename(__FILE__) . '">';
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

	echo "</table><p><input type='Submit' name='ShowPR' value='" . _('Show SSS Premium') . "'>";
	echo "<p><input type='Submit' name='PrintPDF' value='" . _('PrintPDF') . "'>";

	include ('includes/footer.php');
}
/*end of else not PrintPDF */

?>