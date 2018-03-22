<?php
if (isset($_POST['PrintPDF']) and isset($_POST['FSMonth']) and $_POST['FSMonth'] >= 0 and isset($_POST['FSYear']) and $_POST['FSYear'] >= 0) {

	include ('config.php');
	include ('includes/PDFStarter.php');
	include ('includes/ConnectDB.php');
	include ('includes/DateFunctions.php');
	include ('includes/prlFunctions.php');

	$FontSize = 12;
	$PDF->addinfo('Title', _('Basic Pay Monthly Premium'));
	$PDF->addinfo('Subject', _('Basic Pay Monthly Premium'));

	$PageNumber = 0;
	$line_height = 12;

	if ($_POST['FSMonth'] == 0) {
		$Title = _('Basic Pay Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Month not selected'), 'error');
		echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include ('includes/footer.php');
		exit;
	}
	if ($_POST['FSYear'] == 0) {
		$Title = _('Basic Pay Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Year not selected'), 'error');
		echo '<br /><a href"' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include ('includes/footer.php');
		exit;
	}
	$BasicPayMonth = $_POST['FSMonth'];
	$BasicPayYear = $_POST['FSYear'];
	$BasicPayMonthStr = GetMonthStr($BasicPayMonth);
	$PageNumber = 0;
	$FontSize = 10;
	$line_height = 12;
	$FullName = '';
	$BasicPayhNumber = '';
	$BasicPayER = 0;
	$BasicPayEC = 0;
	$BasicPayEE = 0;
	$BasicPayTotal = 0;

	include ('includes/PDFPhilHealthPageHeader.php');

	$SQL = "SELECT employeeid,employerph,employeeph,total
			FROM prlempbasicpayfile
			WHERE prlempbasicpayfile.fsmonth='" . $BasicPayMonth . "'
			AND prlempbasicpayfile.fsyear='" . $BasicPayYear . "'";
	$BasicPayDetails = DB_query($SQL);
	if (DB_num_rows($BasicPayDetails) > 0) {
		//although it is assume that PhilHealth deduction once only every month but who knows
		while ($phrow = DB_fetch_array($BasicPayDetails)) {
			$EmpID = $phrow['employeeid'];
			$FullName = GetName($EmpID);
			$BasicPayNumber = GetEmpRow($EmpID, 21);
			$BasicPayER = $phrow['employerbasicpay'];
			$BasicPayEE = $phrow['employeebasicpay'];
			$BasicPayTotal = $phrow['total'];
			$BasicPayER+= $BasicPayER;
			$BasicPayEE+= $BasicPayEE;
			$BasicPayTotal+= $BasicPayTotal;
			//$YPos -= (2 * $line_height);  //double spacing
			if ($PhilHealthTotal > 0) {
				$FontSize = 8;
				$PDF->selectFont('./fonts/Helvetica.afm');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, $FullName);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 200, $YPos, 50, $FontSize, $BasicPayNumber, 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($BasicPayER, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($BasicPayEE, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($BasicPayTotal, 2), 'right');
				$YPos-= $line_height;
				if ($YPos < ($Bottom_Margin)) {
					include ('includes/PDFPhilHealthPageHeader.php');
				}
			}
		}
	}
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);
	$YPos-= (2 * $line_height);
	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, 'Grand Total');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($GTBasicPayER, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($GTBasicPayEE, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($GTBasicPayTotal, 2), 'right');
	$LeftOvers = $PDF->line($Page_Width - $Right_Margin, $YPos, $Left_Margin, $YPos);

	$buf = $PDF->output();
	$len = strlen($buf);

	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=PHListing.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$PDF->stream();

} elseif (isset($_POST['ShowPR'])) {
	include ('includes/session.php');
	$Title = _('Basic pay Monthly Premium Listing');
	include ('includes/header.php');
	echo 'Use PrintPDF instead';
	echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
	include ('includes/footer.php');
	exit;
} else {
	/*The option to print PDF was not hit */

	include ('includes/session.php');
	$Title = _('Basic Pay Monthly Premium Listing');
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

	echo "</table><p><input type='Submit' name='ShowPR' value='" . _('Show Basic Pay Premium') . "'>";
	echo "<p><input type='Submit' name='PrintPDF' value='" . _('PrintPDF') . "'>";

	include ('includes/footer.php');
}
/*end of else not PrintPDF */

?>