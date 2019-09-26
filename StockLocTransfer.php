<?php
include ('includes/DefineStockLocTransfers.php');
include ('includes/session.php');
$Title = _('Inventory Location Transfer Shipment');
$BookMark = 'LocationTransfers';
$ViewTopic = 'Inventory';
include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if (empty($_GET['Identifier'])) {
	$Identifier = date('U');
} else {
	$Identifier = $_GET['Identifier'];
}

if (!isset($_SESSION['StockTransfer' . $Identifier])) {
	$_SESSION['StockTransfer' . $Identifier] = new StockLocationTransfer();
}

if (isset($_POST['FromStockLocation'])) {
	$_SESSION['StockTransfer' . $Identifier]->FromStockLocation = $_POST['FromStockLocation'];
	$_SESSION['StockTransfer' . $Identifier]->ToStockLocation = $_POST['ToStockLocation'];
	$SQL = "SELECT locationname FROM locations WHERE loccode='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	$_SESSION['StockTransfer' . $Identifier]->FromStockLocationName = $MyRow['locationname'];

	$SQL = "SELECT locationname FROM locations WHERE loccode='" . $_SESSION['StockTransfer' . $Identifier]->ToStockLocation . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	$_SESSION['StockTransfer' . $Identifier]->ToStockLocationName = $MyRow['locationname'];
}

$SQL = "SELECT id FROM container WHERE parentid='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'";
$Result = DB_query($SQL);
if (DB_num_rows($Result) > 0) {
	$WarehouseDefined = true;
} else {
	$WarehouseDefined = false;
}

if (isset($_GET['Delete'])) { //check box to delete the item is set
	unset($_SESSION['StockTransfer' . $Identifier]->StockID[$_GET['Delete']]);
	unset($_SESSION['StockTransfer' . $Identifier]->StockQTY[$_GET['Delete']]);
	unset($_SESSION['StockTransfer' . $Identifier]->Container[$_GET['Delete']]);
	--$_SESSION['StockTransfer' . $Identifier]->LinesCounter;
}

if (isset($_POST['Submit']) or isset($_POST['EnterMoreItems'])) {

	foreach ($_POST as $Key => $Value) {
		if (mb_substr($Key, 0, 7) == 'StockID' and $Value != '') {
			$Index = mb_substr($Key, 7);
			$_SESSION['StockTransfer' . $Identifier]->StockID[$Index] = $Value;
			$_SESSION['StockTransfer' . $Identifier]->StockQTY[$Index] = $_POST['StockQTY' . $Index];
			$_SESSION['StockTransfer' . $Identifier]->Container[$Index] = $_POST['Container_' . $Index];
			++$_SESSION['StockTransfer' . $Identifier]->LinesCounter;
		}
	}

	/*Trap any errors in input */
	$InputError = False;
	/*Start off hoping for the best */
	$TotalItems = 0;

	if ($_FILES['SelectedTransferFile']['name']) { //start file processing
		//initialize
		$InputError = false;
		$ErrorMessage = '';
		//get file handle
		$FileHandle = fopen($_FILES['SelectedTransferFile']['tmp_name'], 'r');
		$TotalItems = 0;
		//loop through file rows
		while (($MyRow = fgetcsv($FileHandle, 10000, ',')) !== false) {

			if (count($MyRow) != 2) {
				prnMsg(_('File contains') . ' ' . count($MyRow) . ' ' . _('columns, but only 2 columns are expected. The comma separated file should have just two columns the first for the item code and the second for the quantity to transfer'), 'error');
				fclose($FileHandle);
				include ('includes/footer.php');
				exit;
			}

			// cleanup the data (csv files often import with empty strings and such)
			$StockId = '';
			$Quantity = 0;
			for ($i = 0;$i < count($MyRow);$i++) {
				switch ($i) {
					case 0:
						$StockId = trim(mb_strtoupper($MyRow[$i]));
						$Result = DB_query("SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $StockId . "'");
						$StockIdCheck = DB_fetch_row($Result);
						if ($StockIdCheck[0] == 0) {
							$InputError = True;
							$ErrorMessage.= _('The part code entered of') . ' ' . $StockId . ' ' . _('is not set up in the database') . '. ' . _('Only valid parts can be entered for transfers') . '<br />';
						}
					break;
					case 1:
						$Quantity = filter_number_format($MyRow[$i]);
						if (!is_numeric($Quantity)) {
							$InputError = True;
							$ErrorMessage.= _('The quantity entered for') . ' ' . $StockId . ' ' . _('of') . $Quantity . ' ' . _('is not numeric.') . _('The quantity entered for transfers is expected to be numeric');
						}
					break;
				} // end switch statement
				if ($_SESSION['ProhibitNegativeStock'] == 1) {
					$InTransitSQL = "SELECT SUM(shipqty-recqty) as intransit
									FROM loctransfers
									WHERE stockid='" . $StockId . "'
										AND shiploc='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'
										AND shipqty>recqty";
					$InTransitResult = DB_query($InTransitSQL);
					$InTransitRow = DB_fetch_array($InTransitResult);
					$InTransitQuantity = $InTransitRow['intransit'];
					// Only if stock exists at this location
					$Result = DB_query("SELECT quantity
										FROM locstock
										WHERE stockid='" . $StockId . "'
										AND loccode='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'");
					$CheckStockRow = DB_fetch_array($Result);
					if (($CheckStockRow['quantity'] - $InTransitQuantity) < $Quantity) {
						$InputError = True;
						$ErrorMessage.= _('The item') . ' ' . $StockId . ' ' . _('does not have enough stock available (') . ' ' . $CheckStockRow['quantity'] . ')' . ' ' . _('The quantity required to transfer was') . ' ' . $Quantity . '.<br />';
					}
				}
			} // end for loop through the columns on the row being processed
			if ($StockId != '' and $Quantity != 0) {
				$_POST['StockID' . $TotalItems] = $StockId;
				$_POST['StockQTY' . $TotalItems] = $Quantity;
				$StockId = '';
				$Quantity = 0;
				$TotalItems++;
			}
		} //end while there are lines in the CSV file
		$_SESSION['StockTransfer' . $Identifier]->LinesCounter = $TotalItems;
	} //end if there is a CSV file to import
	else { // process the manually input lines
		$ErrorMessage = '';

		if (isset($_POST['ClearAll'])) {
			unset($_POST['EnterMoreItems']);
			for ($i = $_SESSION['StockTransfer' . $Identifier]->LinesCounter - 10;$i < $_SESSION['StockTransfer' . $Identifier]->LinesCounter;$i++) {
				unset($_SESSION['StockTransfer' . $Identifier]->StockID[$i]);
				unset($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]);
			}
		}
		$StockIdAccQty = array(); //set an array to hold all items' quantity
		for ($i = 0;$i < $_SESSION['StockTransfer' . $Identifier]->LinesCounter;$i++) {

			if (isset($_SESSION['StockTransfer' . $Identifier]->StockID[$i]) and $_SESSION['StockTransfer' . $Identifier]->StockID[$i] != '') {
				$_SESSION['StockTransfer' . $Identifier]->StockID[$i] = trim(mb_strtoupper($_SESSION['StockTransfer' . $Identifier]->StockID[$i]));
				$Result = DB_query("SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . "'");
				$MyRow = DB_fetch_row($Result);
				if ($MyRow[0] == 0) {
					$InputError = True;
					$ErrorMessage.= _('The part code entered of') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . ' ' . _('is not set up in the database') . '. ' . _('Only valid parts can be entered for transfers') . '<br />';
					$_SESSION['StockTransfer' . $Identifier]->LinesCounter-= 10;
				}
				DB_free_result($Result);
				if (!is_numeric(filter_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]))) {
					$InputError = True;
					$ErrorMessage.= _('The quantity entered of') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockQTY[$i] . ' ' . _('for part code') . ' ' . $_POST['StockID' . $i] . ' ' . _('is not numeric') . '. ' . _('The quantity entered for transfers is expected to be numeric') . '<br />';
					$_SESSION['StockTransfer' . $Identifier]->LinesCounter-= 10;
				}
				if (filter_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]) <= 0) {
					$InputError = True;
					$ErrorMessage.= _('The quantity entered for') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . ' ' . _('is less than or equal to 0') . '. ' . _('Please correct this or remove the item') . '<br />';
					$_SESSION['StockTransfer' . $Identifier]->LinesCounter-= 10;
				}
				if ($_SESSION['ProhibitNegativeStock'] == 1) {
					$InTransitSQL = "SELECT SUM(shipqty-recqty) as intransit
										FROM loctransfers
										WHERE stockid='" . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . "'
											AND shiploc='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'
											AND shipqty>recqty";
					$InTransitResult = DB_query($InTransitSQL);
					$InTransitRow = DB_fetch_array($InTransitResult);
					$InTransitQuantity = $InTransitRow['intransit'];
					// Only if stock exists at this location
					$Result = DB_query("SELECT quantity
											FROM locstock
											WHERE stockid='" . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . "'
											AND loccode='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "'");

					$MyRow = DB_fetch_array($Result);
					if (($MyRow['quantity'] - $InTransitQuantity) < filter_number_format($_SESSION['StockQTY' . $i])) {
						$InputError = True;
						$ErrorMessage.= _('The part code entered of') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . ' ' . _('does not have enough stock available for transfer.') . '.<br />';
						$_SESSION['StockTransfer' . $Identifier]->LinesCounter-= 10;
					}
				}
				// Check the accumulated quantity for each item
				if (isset($StockIdAccQty[$_SESSION['StockTransfer' . $Identifier]->StockID[$i]])) {
					$StockIdAccQty[$_SESSION['StockTransfer' . $Identifier]->StockID[$i]]+= filter_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]);
					if ($MyRow[0] < $StockIdAccQty[$_SESSION['StockTransfer' . $Identifier]->StockID[$i]]) {
						$InputError = True;
						$ErrorMessage.= _('The part code entered of') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . ' ' . _('does not have enough stock available for transter due to accumulated quantity is over quantity on hand.') . '<br />';
						$_SESSION['StockTransfer' . $Identifier]->LinesCounter-= 10;
					}
				} else {
					$StockIdAccQty[$_SESSION['StockTransfer' . $Identifier]->StockID[$i]] = filter_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]);
				} //end of accumulated check
				DB_free_result($Result);
				$TotalItems++;
			}
		} //for all LinesCounter
		
	}

	if ($TotalItems == 0) {
		$InputError = True;
		$ErrorMessage.= _('You must enter at least 1 Stock Item to transfer');
	}

	/*Ship location and Receive location are different */
	if ($_SESSION['StockTransfer' . $Identifier]->FromStockLocation == $_SESSION['StockTransfer' . $Identifier]->ToStockLocation and !$WarehouseDefined) {
		$InputError = True;
		$ErrorMessage.= _('The transfer must have a different location to receive into and location sent from');
	}
	//end if the transfer is not a duplicated
	if (isset($InputError) and $InputError == true) {
		prnMsg($ErrorMessage, 'error');
	}

}

if (isset($_POST['Submit']) and $InputError == False) {

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to BEGIN Location Transfer transaction');
	//Get next Inventory Transfer Shipment Reference Number
	if (isset($_GET['Trf_ID'])) {
		$Trf_ID = $_GET['Trf_ID'];
	} elseif (isset($_POST['Trf_ID'])) {
		$Trf_ID = $_POST['Trf_ID'];
	}

	if (!isset($Trf_ID)) {
		$Trf_ID = GetNextTransNo(16);
	}

	DB_Txn_Begin();
	for ($i = 0;$i < $_SESSION['StockTransfer' . $Identifier]->LinesCounter;$i++) {

		if ($_SESSION['StockTransfer' . $Identifier]->StockID[$i] != '') {
			$DecimalsSql = "SELECT decimalplaces
							FROM stockmaster
							WHERE stockid='" . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . "'";
			$DecimalResult = DB_query($DecimalsSql);
			$DecimalRow = DB_fetch_array($DecimalResult);
			if (!isset($_SESSION['StockTransfer' . $Identifier]->Container[$i])) {
				$_SESSION['StockTransfer' . $Identifier]->Container[$i] = $_SESSION['StockTransfer' . $Identifier]->FromStockLocation;
			}
			$SQL = "INSERT INTO loctransfers (reference,
								stockid,
								shipqty,
								shipdate,
								shiploc,
								shiploccontainer,
								recloc)
						VALUES ('" . $Trf_ID . "',
							'" . $_SESSION['StockTransfer' . $Identifier]->StockID[$i] . "',
							'" . round(filter_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i]), $DecimalRow['decimalplaces']) . "',
							CURRENT_TIMESTAMP,
							'" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "',
							'" . $_SESSION['StockTransfer' . $Identifier]->Container[$i] . "',
							'" . $_SESSION['StockTransfer' . $Identifier]->ToStockLocation . "')";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for') . ' ' . $_SESSION['StockTransfer' . $Identifier]->StockID[$i];
			$ResultLocShip = DB_query($SQL, $ErrMsg);
		}
	}
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to COMMIT Location Transfer transaction');
	DB_Txn_Commit();

	prnMsg(_('The inventory transfer records have been created successfully'), 'success');
	echo '<p><a href="' . $RootPath . '/PDFStockLocTransfer.php?TransferNo=' . urlencode($Trf_ID) . '">' . _('Print the Transfer Docket') . '</a></p>';
	include ('includes/footer.php');

} else {

	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Dispatch'), '" alt="" />', $Title, '
		</p>';

	echo '<form enctype="multipart/form-data" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?Identifier=', urlencode($Identifier), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	if (!isset($_SESSION['StockTransfer' . $Identifier]->FromStockLocation)) {
		echo '<fieldset>';
		echo '<legend>', _('Inventory Location Transfer Shipment'), '</legend>';

		$SQL = "SELECT locations.loccode,
						locationname
					FROM locations
					INNER JOIN locationusers
						ON locationusers.loccode=locations.loccode
						AND locationusers.userid='" . $_SESSION['UserID'] . "'
						AND locationusers.canupd=1
					ORDER BY locationname";
		$ResultStkLocs = DB_query($SQL);

		echo '<field>
				<label for="FromStockLocation">', _('From Stock Location'), ':</label>
				<select required="required" autofocus="autofocus" name="FromStockLocation">';

		while ($MyRow = DB_fetch_array($ResultStkLocs)) {
			if (isset($_SESSION['StockTransfer' . $Identifier]->FromStockLocation)) {
				if ($MyRow['loccode'] == $_SESSION['StockTransfer' . $Identifier]->FromStockLocation) {
					echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				} else {
					echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				}
			} elseif ($MyRow['loccode'] == $_SESSION['UserStockLocation']) {
				echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				$_SESSION['StockTransfer' . $Identifier]->FromStockLocation = $MyRow['loccode'];
			} else {
				echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
			}
		}
		echo '</select>
			<fieldhelp>', _('Select the location where the stock is to be moved from.'), '</fieldhelp>
		</field>';

		DB_data_seek($ResultStkLocs, 0);
		echo '<field>
				<label for="ToStockLocation">', _('To Stock Location'), ':</label>
				<select required="required" name="ToStockLocation">';
		while ($MyRow = DB_fetch_array($ResultStkLocs)) {
			if (isset($_SESSION['StockTransfer' . $Identifier]->ToStockLocation)) {
				if ($MyRow['loccode'] == $_SESSION['StockTransfer' . $Identifier]->ToStockLocation) {
					echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				} else {
					echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				}
			} elseif ($MyRow['loccode'] == $_SESSION['UserStockLocation']) {
				echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
				$_SESSION['StockTransfer' . $Identifier]->ToStockLocation = $MyRow['loccode'];
			} else {
				echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
			}
		}
		echo '</select>
			<fieldhelp>', _('Select the location where the stock is to be moved to.'), '</fieldhelp>
		</field>';

		echo ' </fieldset>';

		echo '<div class="centre">
				<input type="submit" name="SelectLocations" value="', _('Select Locations'), '" />
			</div>';

	} else {
		echo '<form enctype="multipart/form-data" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?Identifier=', $Identifier, '" method="post">';
		echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
		echo '<fieldset>
				<legend>', _('Stock transfer details.'), '</legend>
				<field>
					<label for="FromStockLocation">', _('From Stock Location'), '</label>
					<div class="fieldtext">', $_SESSION['StockTransfer' . $Identifier]->FromStockLocationName, ' (', $_SESSION['StockTransfer' . $Identifier]->FromStockLocation, ')</div>
					<input type="hidden" name="FromStockLocation" value="', $_SESSION['StockTransfer' . $Identifier]->FromStockLocation, '" />
				</field>
				<field>
					<label for="ToStockLocation">', _('To Stock Location'), '</label>
					<div class="fieldtext">', $_SESSION['StockTransfer' . $Identifier]->ToStockLocationName, ' (', $_SESSION['StockTransfer' . $Identifier]->ToStockLocation, ')</div>
					<input type="hidden" name="ToStockLocation" value="', $_SESSION['StockTransfer' . $Identifier]->ToStockLocation, '" />
				</field>
				<field>
					<label for="SelectedTransferFile">', _('Upload CSV file of Transfer Items and Quantites'), ':</label>
					<input name="SelectedTransferFile" type="file" />
				</field>';

		echo '<h2>', _('OR enter stock details below'), '</h2>';

		echo '<table>
				<tr>
					<th>', _('Item Code'), '</th>
					<th>', _('Quantity'), '</th>';
		if ($WarehouseDefined == true) {
			echo '<th>', _('Container'), '</th>';
		}

		echo '</tr>';

		$j = 0;
		/* row counter for reindexing */
		if (isset($_SESSION['StockTransfer' . $Identifier]->LinesCounter)) {

			for ($i = 0;$i < $_SESSION['StockTransfer' . $Identifier]->LinesCounter;$i++) {
				if (!isset($_SESSION['StockTransfer' . $Identifier]->StockID[$i])) {
					continue;
				}
				if ($_SESSION['StockTransfer' . $Identifier]->StockID[$i] == '') {
					break;
				}

				echo '<tr>
						<td><input type="text" name="StockID', $j, '" size="21" maxlength="20" value="', $_SESSION['StockTransfer' . $Identifier]->StockID[$i], '" /></td>
						<td><input type="text" name="StockQTY', $j, '" size="10" required="required" maxlength="10" class="number" value="', locale_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$i], 'Variable'), '" /></td>';

				if ($WarehouseDefined) {
					$ContainerSQL = "SELECT id, name FROM container WHERE location='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "' AND parentid<>''";
					$ContainerResult = DB_query($ContainerSQL);
					echo '<td>
							<select name="Container_', $j, '">';
					while ($MyContainerRow = DB_fetch_array($ContainerResult)) {
						if (isset($_SESSION['StockTransfer' . $Identifier]->Container[$j]) and $_SESSION['StockTransfer' . $Identifier]->Container[$j] == $MyContainerRow['id']) {
							echo '<option selected="selected" value="', $MyContainerRow['id'], '">', $MyContainerRow['name'], '</option>';
						} else {
							echo '<option value="', $MyContainerRow['id'], '">', $MyContainerRow['name'], '</option>';
						}
					}
				}
				echo '<td><a href="', $RootPath, '/StockLocTransfer.php?Delete=', urlencode($j), '&Identifier=', urlencode($Identifier), '">', _('Delete'), '</a></td>';
				echo '</tr>';
				++$j;
			}
		} else {
			$j = 0;
		}
		// $i is incremented an extra time, so 9 to get 10...
		$z = ($j + 9);

		while ($j < $z) {
			if (!isset($_SESSION['StockTransfer' . $Identifier]->StockID[$j])) {
				$_SESSION['StockTransfer' . $Identifier]->StockID[$j] = '';
			}
			if (!isset($_SESSION['StockTransfer' . $Identifier]->StockQTY[$j])) {
				$_SESSION['StockTransfer' . $Identifier]->StockQTY[$j] = 0;
			}
			echo '<tr>
					<td><input type="text" name="StockID', $j, '" size="21" maxlength="20" value="', $_SESSION['StockTransfer' . $Identifier]->StockID[$j], '" /></td>
					<td><input type="text" name="StockQTY', $j, '" size="10" required="required" maxlength="10" class="number" value="', locale_number_format($_SESSION['StockTransfer' . $Identifier]->StockQTY[$j]), '" /></td>';

			if ($WarehouseDefined) {
				$ContainerSQL = "SELECT id, name FROM container WHERE location='" . $_SESSION['StockTransfer' . $Identifier]->FromStockLocation . "' AND parentid<>''";
				$ContainerResult = DB_query($ContainerSQL);
				echo '<td>
						<select name="Container_', $j, '">';
				while ($MyContainerRow = DB_fetch_array($ContainerResult)) {
					if (isset($_SESSION['StockTransfer' . $Identifier]->Container[$j]) and $_SESSION['StockTransfer' . $Identifier]->Container[$j] == $MyContainerRow['id']) {
						echo '<option selected="selected" value="', $MyContainerRow['id'], '">', $MyContainerRow['name'], '</option>';
					} else {
						echo '<option value="', $MyContainerRow['id'], '">', $MyContainerRow['name'], '</option>';
					}
				}
			}

			echo '</tr>';
			++$j;
		}

		echo '</table>
			</fieldset>';

		echo '<div class="centre">
				<input type="hidden" name="LinesCounter" value="', $j, '" />
				<input type="submit" name="EnterMoreItems" value="', _('Add More Items'), '" />
				<input type="submit" name="Submit" value="', _('Create Transfer Shipment'), '" />
			</div>';

		echo '</form>';
	}
	include ('includes/footer.php');
}
?>