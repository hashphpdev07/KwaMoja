<?php
// BEGIN: Functions division ---------------------------------------------------
function PageHeader() {
	global $PDF;
	global $Page_Width;
	global $Page_Height;
	global $Top_Margin;
	global $Bottom_Margin;
	global $Left_Margin;
	global $Right_Margin;
	global $PageNumber;
	global $YPos;
	global $FontSize;
	global $line_height;
	global $SalesTypeName;
	global $CustomerName;
	$PageNumber++; // Increments $PageNumber before printing.
	if ($PageNumber > 1) { // Inserts a page break if it is not the first page.
		$PDF->newPage();
	}
	$YPos = $Page_Height - $Top_Margin;
	$FontSizeLast = $FontSize; // To preserve the main font size.
	$FontSize = 10;
	$PDF->addText($Left_Margin, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']); // Company name.
	$PDF->addTextWrap($Page_Width - $Right_Margin - 140, $YPos - $FontSize, 140, $FontSize, _('Page') . ' ' . $PageNumber, 'right'); // Page number.
	$YPos-= $FontSize;
	//Note, this is ok for multilang as this is the value of a Select, text in option is different
	if ($_POST['CustomerSpecials'] == _('Customer Special Prices Only')) {
		$PDF->addText($Left_Margin, $YPos, $FontSize, _('Price List') . ': ' . $CustomerName);
	} else {
		$PDF->addText($Left_Margin, $YPos, $FontSize, _('Price List') . ': ' . $SalesTypeName);
	}
	$PDF->addTextWrap($Page_Width - $Right_Margin - 140, $YPos - $FontSize, 140, $FontSize, _('Printed') . ': ' . date($_SESSION['DefaultDateFormat']), 'right'); // Date printed.
	$YPos-= $FontSize;
	$PDF->addText($Left_Margin, $YPos, $FontSize, _('Effective As At') . ' ' . $_POST['EffectiveDate']);
	$PDF->addTextWrap($Page_Width - $Right_Margin - 140, $YPos - $FontSize, 140, $FontSize, date('H:i:s'), 'right'); // Time printed.
	$YPos-= (2 * $line_height);
	// Draws a rectangle to put the headings in:
	$PDF->Rectangle($Left_Margin, // Rectangle $XPos.
	$YPos, // Rectangle $YPos.
	$Page_Width - $Left_Margin - $Right_Margin, // Rectangle $Width.
	$line_height * 2); // Rectangle $Height.
	$YPos-= $line_height;
	/*set up the headings */
	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 80, $FontSize, _('Item Code')); // 20chr @ 8dpi.
	if ($LeftOvers != '') { // If translated text is greater than column width, prints remainder.
		$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos - $FontSize, 80, $FontSize, $LeftOvers);
	}
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 80, $YPos, 200, $FontSize, _('Item Description')); // 50chr @ 8dpi.
	if ($LeftOvers != '') { // If translated text is greater than column width, prints remainder.
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 80, $YPos - $FontSize, 200, $FontSize, $LeftOvers);
	}
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 280, $YPos, 96, $FontSize, _('Effective Date Range'), 'center'); // (10+2+12)chr @ 8dpi.
	if ($LeftOvers != '') { // If translated text is greater than column width, prints remainder.
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 280, $YPos - $FontSize, 96, $FontSize, $LeftOvers, 'center');
	}
	if ($_POST['CustomerSpecials'] == 'Customer Special Prices Only') {
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 376, $YPos, 160, $FontSize, _('Branch')); // 40chr @ 8dpd.
		
	}
	if ($_POST['ShowGPPercentages'] == 'Yes') {
		$LeftOvers = $PDF->addTextWrap($Page_Width - $Right_Margin - 128, $YPos, 32, $FontSize, _('Gross Profit'), 'right'); // 8chr @ 8dpi.
		if ($LeftOvers != '') { // If translated text is greater than column width, prints remainder.
			$LeftOvers = $PDF->addTextWrap($Page_Width - $Right_Margin - 128, $YPos - $FontSize, 32, $FontSize, $LeftOvers, 'right');
		}
	}
	$LeftOvers = $PDF->addTextWrap($Page_Width - $Right_Margin - 96, $YPos, 96, $FontSize, _('Price'), 'right'); // 24chr @ 8dpd.
	$YPos-= $FontSize;
	// In some countries it is mandatory to clarify that prices do not include taxes:
	$PDF->addText($Left_Margin, $YPos, $FontSize, '* ' . _('Prices excluding tax')); // Warning text.
	$YPos-= $FontSize; // End-of-line line-feed.*/
	/*	$YPos -= $FontSize;// Jumps additional line after the table headings.*/
	$FontSize = $FontSizeLast; // Resets to the main font size.
	
}
// END: Functions division -----------------------------------------------------

?>