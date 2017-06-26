<?php
/* $Id: Z_ImportSuppliers.php 6067 2013-07-10 02:04:22Z tehonu $*/
$PageSecurity=1;
include('includes/session.php');
$Title = _('Import Purchase Data');
include('includes/header.php');

if (isset($_POST['FormID'])) {
	if (!isset($_POST['UpdateIfExists'])) {
		$_POST['UpdateIfExists'] = 0;
	} else {
		$_POST['UpdateIfExists'] = 1;
	}
} else {
	$_POST['UpdateIfExists'] = 0;
}
// If this script is called with a file object, then the file contents are imported
// If this script is called with the gettemplate flag, then a template file is served
// Otherwise, a file upload form is displayed

$FieldHeadings = array(
	'SupplierID', //0
	'StockID', //1
	'Price', //2
	'QtyGreaterThan', //3
	'SuppliersUOM', //4
	'ConversionFactor', //5
	'SupplierDescription', //6
	'LeadTime', //7
	'Preferred', //8
	'EffectiveFrom', //9
	'SuppliersPartCode', //10
	'MinimumOrderQty' //11
);

if (isset($_FILES['userfile']) and $_FILES['userfile']['name']) { //start file processing

	//initialize
	$FieldTarget = count($FieldHeadings);
	$InputError = 0;

	//check file info
	$FileName = $_FILES['userfile']['name'];
	$TempName = $_FILES['userfile']['tmp_name'];
	$FileSize = $_FILES['userfile']['size'];

	//get file handle
	if ($FileHandle = fopen($TempName, 'r')) {
		echo 'True';
	} else {
		echo 'False';
	}

	//get the header row
	$headRow = fgetcsv($FileHandle);

	//check for correct number of fields
	if (count($headRow) != count($FieldHeadings)) {
		prnMsg(_('File contains ' . count($headRow) . ' columns, expected ' . count($FieldHeadings) . '. Try downloading a new template.'), 'error');
		fclose($FileHandle);
		include('includes/footer.php');
		exit;
	}

	//test header row field name and sequence
	$head = 0;
	foreach ($headRow as $headField) {
		if (mb_strtoupper($headField) != mb_strtoupper($FieldHeadings[$head])) {
			prnMsg(_('File contains incorrect headers (' . mb_strtoupper($headField) . ' != ' . mb_strtoupper($header[$head]) . '. Try downloading a new template.'), 'error');
			fclose($FileHandle);
			include('includes/footer.php');
			exit;
		}
		$head++;
	}

	//start database transaction
	DB_Txn_Begin();

	//loop through file rows
	$row = 1;
	$InsertNum = 0;
	while (($filerow = fgetcsv($FileHandle, 10000, ",")) !== FALSE) {
		//check for correct number of fields
		$fieldCount = count($filerow);
		if ($fieldCount != $FieldTarget) {
			prnMsg(_($FieldTarget . ' fields required, ' . $fieldCount . ' fields received'), 'error');
			fclose($FileHandle);
			include('includes/footer.php');
			exit;
		}

		// cleanup the data (csv files often import with empty strings and such)
		foreach ($filerow as &$Value) {
			$Value = trim($Value);
		}

		$SupplierID = $filerow[0];
		$StockID = $filerow[1];
		$Price = $filerow[2];
		$QtyGreaterThan = $filerow[3];
		$SuppliersUOM = $filerow[4];
		$ConversionFactor = $filerow[5];
		$SupplierDescription = $filerow[6];
		$LeadTime = $filerow[7];
		$Preferred = $filerow[8];
		$EffectiveFrom = $filerow[9];
		$SuppliersPartCode = $filerow[10];
		$MinimumOrderQty = $filerow[11];
		//initialise no input errors assumed initially before we test
		$InputError = 0;
		/* actions to take once the user has clicked the submit button
		ie the page has called itself with some user input */

		$SQL = "SELECT COUNT(supplierid) FROM suppliers WHERE supplierid='" . $SupplierID . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] == 0) {
			$InputError = 1;
			prnMsg(_('The supplier code does not exist in  the database'), 'error');
		}

		$SQL = "SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $StockID . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] == 0) {
			$InputError = 1;
			prnMsg(_('The Part Code does not exist in the bdatabase'), 'error');
		}
		if (!filter_var($Price, FILTER_VALIDATE_FLOAT)) {
			$InputError = 1;
			prnMsg(_('The price needs to be a valid floating point number'), 'error');
		}
		if (!filter_var($QtyGreaterThan, FILTER_VALIDATE_FLOAT)) {
			$InputError = 1;
			prnMsg(_('The Quantity greater than figure needs to be a valid floating point number'), 'error');
		}
		if (mb_strlen($SuppliersUOM) > 50) {
			$InputError = 1;
			prnMsg(_('The suppliers unit of measure must be 50 characters or less long.'), 'error');
		}
		if (!filter_var($ConversionFactor, FILTER_VALIDATE_FLOAT)) {
			$InputError = 1;
			prnMsg(_('The conversion factor needs to be a valid floating point number'), 'error');
		}
		if (mb_strlen($SupplierDescription) > 50) {
			$InputError = 1;
			prnMsg(_('The suppliers product description must be 50 characters or less long.'), 'error');
		}
		if (!filter_var($LeadTime, FILTER_VALIDATE_INT)) {
			$InputError = 1;
			prnMsg(_('The lead time should be an integer value'), 'error');
		}
		if ($Preferred != 0 and $Preferred != 1) {
			$InputError = 1;
			prnMsg(_('The preferred flag must be a one or a zero'), 'error');
		}
		if (!is_date($EffectiveFrom)) {
			$InputError = 1;
			prnMsg(_('The effective from date is not in the correct format'), 'error');
		}
		if (mb_strlen($SuppliersPartCode) > 50) {
			$InputError = 1;
			prnMsg(_('The suppliers item code must be 50 characters or less long.'), 'error');
		}
		if (!filter_var($MinimumOrderQty, FILTER_VALIDATE_INT)) {
			$InputError = 1;
			prnMsg(_('The minimum order quantity should be an integer value'), 'error');
		}

		if ($InputError != 1) {

			$EffectiveFromDate = FormatDateForSQL($EffectiveFrom);

			$InsertNum++;
			$SQL = "INSERT INTO purchdata (supplierno,
											stockid,
											price,
											qtygreaterthan,
											suppliersuom,
											conversionfactor,
											supplierdescription,
											leadtime,
											preferred,
											effectivefrom,
											suppliers_partno,
											minorderqty)
									VALUES ('" . $SupplierID . "',
											'" . $StockID . "',
											'" . $Price . "',
											'" . $QtyGreaterThan . "',
											'" . $SuppliersUOM . "',
											'" . $ConversionFactor . "',
											'" . $SupplierDescription . "',
											'" . $LeadTime . "',
											'" . $Preferred . "',
											'" . $EffectiveFrom . "',
											'" . $SuppliersPartCode . "',
											'" . $MinimumOrderQty . "'
											)";

			$ErrMsg = _('The supplier record could not be added because');
			$DbgMsg = _('The SQL that was used to insert the supplier but failed was');

			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

		}
	}

	if ($InputError == 1) { //exited loop with errors so rollback
		prnMsg(_('Failed on row ' . $row . '. Batch import has been rolled back.'), 'error');
		DB_Txn_Rollback();
	} else { //all good so commit data transaction
		DB_Txn_Commit();
		prnMsg(_('Batch Import of') . ' ' . $FileName . ' ' . _('has been completed. All transactions committed to the database.'), 'success');
		prnMsg(_('Insert') . ':' . $InsertNum);

	}

	fclose($FileHandle);

} elseif (isset($_POST['gettemplate']) or isset($_GET['gettemplate'])) { //download an import template

	echo '<br /><br /><br />"' . implode('","', $FieldHeadings) . '"<br /><br /><br />';

} else { //show file upload form

	prnMsg(_('Please ensure that your csv file charset is UTF-8, otherwise the data will not store correctly in database'), 'warn');

	echo '
		<br />
		<a href="Z_ImportPurchaseData.php?gettemplate=1">Get Import Template</a>
		<br />
		<br />';
	echo '<form action="Z_ImportPurchaseData.php" method="post" enctype="multipart/form-data">';
	echo '<div class="centre">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />' . _('Upload file') . ': <input name="userfile" type="file" />
			<input type="submit" value="' . _('Send File') . '" />';

	echo '</div>
		</form>';

}

include('includes/footer.php');
?>