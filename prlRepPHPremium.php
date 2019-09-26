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
	$PDF->addinfo('Title', _('PhilHealth Monthly Premium'));
	$PDF->addinfo('Subject', _('PhilHealth Monthly Premium'));

	$PageNumber = 0;
	$line_height = 12;

	if ($_POST['FSMonth'] == 0) {
		$Title = _('PhilHealth Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Month not selected'), 'error');
		echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include ('includes/footer.php');
		exit;
	}
	if ($_POST['FSYear'] == 0) {
		$Title = _('PhilHealth Monthly Premuim Listing') . ' - ' . _('Problem Report');
		include ('includes/header.php');
		prnMsg(_('Year not selected'), 'error');
		echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include ('includes/footer.php');
		exit;
	}
	$PhilHealthMonth = $_POST['FSMonth'];
	$PhilHealthYear = $_POST['FSYear'];
	$PhilHealthMonthStr = GetMonthStr($PhilHealthMonth);
	$PageNumber = 0;
	$FontSize = 10;
	$line_height = 12;
	$FullName = '';
	$PhilHealthNumber = '';
	$PhilHealthER = 0;
	$PhilHealthEC = 0;
	$PhilHealthEE = 0;
	$PhilHealthTotal = 0;

	include ('includes/PDFPhilHealthPageHeader.php');

	$SQL = "SELECT employeeid,employerph,employeeph,total
			FROM prlempphfile
			WHERE prlempphfile.fsmonth='" . $PhilHealthMonth . "'
			AND prlempphfile.fsyear='" . $PhilHealthYear . "'";
	$PhilHealthDetails = DB_query($SQL);
	if (DB_num_rows($PhilHealthDetails) > 0) {
		//although it is assume that PhilHealth deduction once only every month but who knows
		while ($phrow = DB_fetch_array($PhilHealthDetails)) {
			$EmpID = $phrow['employeeid'];
			$FullName = GetName($EmpID);
			$PhilHealthNumber = GetEmpRow($EmpID, 21);
			$PhilHealthER = $phrow['employerph'];
			$PhilHealthEE = $phrow['employeeph'];
			$PhilHealthTotal = $phrow['total'];
			$GTPhilHealthER+= $PhilHealthER;
			$GTPhilHealthEE+= $PhilHealthEE;
			$GTPhilHealthTotal+= $PhilHealthTotal;
			//$YPos -= (2 * $line_height);  //double spacing
			if ($PhilHealthTotal > 0) {
				$FontSize = 8;
				$PDF->selectFont('./fonts/Helvetica.afm');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 150, $FontSize, $FullName);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 200, $YPos, 50, $FontSize, $PhilHealthNumber, 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($PhilHealthER, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($PhilHealthEE, 2), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($PhilHealthTotal, 2), 'right');
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
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 50, $FontSize, number_format($GTPhilHealthER, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 410, $YPos, 50, $FontSize, number_format($GTPhilHealthEE, 2), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 460, $YPos, 50, $FontSize, number_format($GTPhilHealthTotal, 2), 'right');
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
	$Title = _('PhilHealth Monthly Premium Listing');
	include ('includes/header.php');
	echo 'Use PrintPDF instead';
	echo "<BR><A HREF='" . $RootPath . "/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	include ('includes/footer.php');
	exit;
} else { /*The option to print PDF was not hit */

	include ('includes/session.php');
	$Title = _('PhilHealth Monthly Premium Listing');
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

	echo '</table><P><input type="submit" name="ShowPR" value="' . _('Show PhilHealth Premium') . '">';
	echo '<P><input type="submit" name="PrintPDF" value="' . _('PrintPDF') . '">';

	include ('includes/footer.php');;
} /*end of else not PrintPDF */

?>