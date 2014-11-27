<?php
/* $Id$*/

include('includes/session.inc');
$PaperSize = 'T1_portrait';
include('includes/PDFStarter.php');

$FontSize = 16;
$pdf->addInfo('Title', _('Sales Receipt'));

$PageNumber = 1;
$line_height = 17;
$FontSize = 14;
$YPos = $Page_Height - $Top_Margin;
$XPos = 0;

$pdf->addJpegFromFile($_SESSION['LogoFile'], $XPos, $YPos - 30, 0, 60);

$SQL = "SELECT locationname, deladd1 FROM locations WHERE loccode='" . $_SESSION['UserStockLocation'] . "'";
$Result = DB_query($SQL);
$mylocationrow = DB_fetch_array($Result);

$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 1), 300, $FontSize, $mylocationrow['deladd1']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 2), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice1']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 3), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 4), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice3']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 5), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice4']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 6), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice5']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 7), 300, $FontSize, $_SESSION['CompanyRecord']['regoffice6']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 8), 150, $FontSize, _('Customer Receipt Number ') . '  : ' . $_GET['FromTransNo']);
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 9), 300, $FontSize, _('Date ') . '  : ' . date('l jS \of F Y h:i:s A'));
$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 10), 140, $FontSize, _('Cashier') . ': ' . $_SESSION['UsersRealName']);
$NameYPos = $YPos - ($line_height * 12);
$SQL = "SELECT MIN(id) as start FROM debtortrans WHERE type=10 AND transno='" . $_GET['FromTransNo'] . "'";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$StartReceiptNumber = $MyRow['start'];

if ($_GET['InvOrCredit'] == 'Invoice') {
	$Type = 10;
} else if ($_GET['InvOrCredit'] == 'Credit') {
	$Type = 11;
} else {
	$Type = 12;
}

$SQL = "SELECT 	debtortrans.debtorno,
				debtortrans.ovamount,
				debtortrans.invtext,
				debtortrans.alloc,
				salesorderdetails.stkcode,
				stockmaster.description,
				salesorderdetails.qtyinvoiced as quantity,
				stockmaster.units,
				salesorderdetails.unitprice
			FROM debtortrans
			INNER JOIN salesorders
				ON salesorders.orderno=debtortrans.order_
			INNER JOIN salesorderdetails
				ON salesorderdetails.orderno=debtortrans.order_
			INNER JOIN stockmaster
				ON stockmaster.stockid=salesorderdetails.stkcode
			WHERE type='" . $Type . "'
				AND transno='" . $_GET['FromTransNo'] . "'";
$MyOrderResult = DB_query($SQL);

$MyRow = DB_fetch_array($MyOrderResult);
$DebtorNo = $MyRow['debtorno'];
if (!isset($_GET['Amount'])) {
	$Amount = $MyRow['alloc'];
} else {
	$Amount = -$_GET['Amount'];
}
$Narrative = $MyRow['invtext'];
DB_data_seek($MyOrderResult, 0);

if ($Type != 12) {
	$LeftOvers = $pdf->addTextWrap(0, $YPos - ($line_height * 11), 140, $FontSize, $Narrative);
}

$YPos -= 170;

$YPos -= $line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different

$YPos -= (2 * $line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line(20, $YPos + $line_height, $Page_Width - $Right_Margin, $YPos + $line_height);

$FontSize = 14;
$YPos -= (1.5 * $line_height);

//$PageNumber++;

$SQL = "SELECT currency,
				currabrev,
				decimalplaces
			FROM currencies
			WHERE currabrev=(SELECT currcode
							FROM banktrans
							WHERE type=10
							AND transno='" . $_GET['FromTransNo'] . "')";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$Currency = $MyRow['currency'];
$CurrCode = $MyRow['currabrev'];
$DecimalPlaces = $MyRow['decimalplaces'];
$SQL = "SELECT name,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				currcode
			FROM debtorsmaster
			WHERE debtorno='" . $DebtorNo . "'";

$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);

$LeftOvers = $pdf->addTextWrap(0, $NameYPos, 300, $FontSize, _('Received From') . ' : ');
$NameYPos = $NameYPos - ($line_height * 1);
$LeftOvers = $pdf->addTextWrap(0, $NameYPos, 300, $FontSize, $DebtorNo . ' ' . htmlspecialchars_decode($MyRow['name']));

if ($Type != 12) {
	while ($mylines = DB_fetch_array($MyOrderResult)) {
		if ($PageNumber > 1) {
			$pdf->newPage();
			$YPos = $Page_Height - $Top_Margin;
			$XPos = 0;
			$PageNumber = 1;
		}
		$LeftOvers = $pdf->addTextWrap(0, $YPos, 300, $FontSize, htmlspecialchars_decode($mylines['description']));
		$YPos = $YPos - ($line_height);
		$LeftOvers = $pdf->addTextWrap(20, $YPos, 300, $FontSize, htmlspecialchars_decode($mylines['quantity']) . ' @ ' . $mylines['unitprice']);
		$LeftOvers = $pdf->addTextWrap(100, $YPos, 300, $FontSize, number_format($mylines['quantity'] * $mylines['unitprice'], 0) . ' ' . $MyRow['currcode']);
		$YPos = $YPos - ($line_height);
		if ($YPos <= 0) {
			$PageNumber++;
		}
	}
} else {
	$YPos = $YPos - ($line_height);
	$LeftOvers = $pdf->addTextWrap(0, $YPos, 300, $FontSize, htmlspecialchars_decode(_('In Patient Deposit')));
}

$YPos = $YPos - ($line_height * 1);
$LeftOvers = $pdf->addTextWrap(50, $YPos, 300, $FontSize, _('Total received') . ' : ');
if ($Type != 12) {
	$LeftOvers = $pdf->addTextWrap(150, $YPos, 300, $FontSize, number_format($Amount, $DecimalPlaces) . '  ' . $MyRow['currcode']);
} else {
	$LeftOvers = $pdf->addTextWrap(150, $YPos, 300, $FontSize, number_format(-$Amount, $DecimalPlaces) . '  ' . $MyRow['currcode']);
}
$YPos = $YPos - ($line_height * 2);

$pdf->OutputD('Receipt-' . $_GET['FromTransNo'], 'I');
?>