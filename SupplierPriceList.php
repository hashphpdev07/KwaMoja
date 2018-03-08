<?php
include ('includes/session.php');

$Title = _('Supplier Purchasing Data');

$ViewTopic = 'PurchaseOrdering';
$BookMark = 'SupplierPriceList';
include ('includes/header.php');
if (isset($_POST['SupplierID'])) {
	$_POST['SupplierID'] = stripslashes($_POST['SupplierID']);
} elseif (isset($_GET['SupplierID'])) {
	$_POST['SupplierID'] = stripslashes($_GET['SupplierID']);
}

if (isset($_POST['StockSearch'])) {
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', ' ', _('Search for Inventory Items'), '
		</p>';

	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<input type="hidden" value="', $_POST['SupplierID'], '" name="SupplierID" />';

	echo '<fieldset>
			<legend>', _('Select a Stock Item'), '</legend>
			<field>
				<label for="StockCat">', _('In Stock Category'), ':</label>
				<select name="StockCat" autofocus="autofocus">';
	if (!isset($_POST['StockCat'])) {
		$_POST['StockCat'] = '';
	}
	if ($_POST['StockCat'] == 'All') {
		echo '<option selected="True" value="All">', _('All'), '</option>';
	} else {
		echo '<option value="All">', _('All'), '</option>';
	}
	$SQL = "SELECT categoryid,
				categorydescription
			FROM stockcategory
			ORDER BY categorydescription";
	$Result1 = DB_query($SQL);
	while ($MyRow1 = DB_fetch_array($Result1)) {
		if ($MyRow1['categoryid'] == $_POST['StockCat']) {
			echo '<option selected="True" value="', $MyRow1['categoryid'], '">', $MyRow1['categorydescription'], '</option>';
		} else {
			echo '<option value="', $MyRow1['categoryid'], '">', $MyRow1['categorydescription'], '</option>';
		}
	}
	echo '</select>
		</field>';

	echo '<field>
			<label for="Keywords">', _('Enter partial'), ' ', _('Description'), ':</label>';
	if (isset($_POST['Keywords'])) {
		echo '<input type="search" name="Keywords" value="', $_POST['Keywords'], '" size="34" maxlength="25" />';
	} else {
		echo '<input type="search" name="Keywords" size="34" maxlength="25" placeholder="" />';
	}
	echo '<fieldhelp>', _('Enter part of the item description'), '</fieldhelp>
		</field>';

	echo '<h3>', _('OR'), ' ', '</h3>';

	echo '<field>
			<label for="StockCode">', _('Enter partial'), ' <b>', _('Stock Code'), '</b>:</label>';
	if (isset($_POST['StockCode'])) {
		echo '<input type="text" autofocus="autofocus" name="StockCode" value="', $_POST['StockCode'], '" size="15" maxlength="18" />';
	} else {
		echo '<input type="text" autofocus="autofocus" name="StockCode" size="15" maxlength="18" />';
	}
	echo '</field>';

	echo '</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="Search" value="', _('Search Now'), '" />
		</div>';
	echo '</form>';
	include ('includes/footer.php');
	exit;
}

if (isset($_POST['Search']) or isset($_POST['Go']) or isset($_POST['Next']) or isset($_POST['Previous'])) {
	if (!isset($_POST['Go']) and !isset($_POST['Next']) and !isset($_POST['Previous'])) {
		// if Search then set to first page
		$_POST['PageOffset'] = 1;
	}
	if ($_POST['Keywords'] and $_POST['StockCode']) {
		prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						FROM stockmaster
						LEFT JOIN stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid,
							locstock
						WHERE stockmaster.stockid=locstock.stockid
						AND stockmaster.description " . LIKE . " '$SearchString'
						AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
						GROUP BY stockmaster.stockid,
							stockmaster.description,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						FROM stockmaster INNER JOIN locstock
						ON stockmaster.stockid=locstock.stockid
						WHERE description " . LIKE . " '$SearchString'
						AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
						AND categoryid='" . $_POST['StockCat'] . "'
						GROUP BY stockmaster.stockid,
							stockmaster.description,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						ORDER BY stockmaster.stockid";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.mbflag,
							stockmaster.discontinued,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							stockmaster.decimalplaces
						FROM stockmaster
						INNER JOIN stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid
						INNER JOIN locstock
						ON stockmaster.stockid=locstock.stockid
						WHERE (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
						AND stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
						GROUP BY stockmaster.stockid,
								stockmaster.description,
								stockmaster.units,
								stockmaster.mbflag,
								stockmaster.discontinued,
								stockmaster.decimalplaces
						ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					stockmaster.discontinued,
					sum(locstock.quantity) as qoh,
					stockmaster.units,
					stockmaster.decimalplaces
				FROM stockmaster INNER JOIN locstock
				ONstockmaster.stockid=locstock.stockid
				WHERE stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
				AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
				AND categoryid='" . $_POST['StockCat'] . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.discontinued,
					stockmaster.decimalplaces
				ORDER BY stockmaster.stockid";
		}
	} elseif (!isset($_POST['StockCode']) and !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					stockmaster.discontinued,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.decimalplaces
				FROM stockmaster
				LEFT JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.discontinued,
					stockmaster.decimalplaces
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					stockmaster.discontinued,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.decimalplaces
				FROM stockmaster INNER JOIN locstock
				ONstockmaster.stockid=locstock.stockid
				WHERE categoryid='" . $_POST['StockCat'] . "'
				AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.discontinued,
					stockmaster.decimalplaces
				ORDER BY stockmaster.stockid";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No stock items were returned by this search please re-enter alternative criteria to try again'), 'info');
	}
	unset($_POST['Search']);
}
/* end query for list of records */
/* display list if there is more than one record */
if (isset($SearchResult) and !isset($_POST['Select'])) {
	echo '<p class="page_title_text" ><img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', ' ', _('Search for Inventory Items'), '</p>';
	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<input type="hidden" value="', stripslashes($_POST['SupplierID']), '" name="SupplierID" />';
	$ListCount = DB_num_rows($SearchResult);
	if ($ListCount > 0) {
		// If the user hit the search button and there is more than one item to show
		$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
		if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}
		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}
		if ($_POST['PageOffset'] > $ListPageMax) {
			$_POST['PageOffset'] = $ListPageMax;
		}
		if ($ListPageMax > 1) {
			echo '<div class="centre">
					&nbsp;&nbsp;', $_POST['PageOffset'], ' ', _('of'), ' ', $ListPageMax, ' ', _('pages'), '. ', _('Go to Page'), ': ';
			echo '<select name="PageOffset">';
			$ListPage = 1;
			while ($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<option value=', $ListPage, ' selected>', $ListPage, '</option>';
				} else {
					echo '<option value=', $ListPage, '>', $ListPage, '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go" value="', _('Go'), '" />
				<input type="submit" name="Previous" value="', _('Previous'), '" />
				<input type="submit" name="Next" value="', _('Next'), '" />';
			echo '<input type="hidden" name=Keywords value="', $_POST['Keywords'], '" />';
			echo '<input type="hidden" name=StockCat value="', $_POST['StockCat'], '" />';
			echo '<input type="hidden" name=StockCode value="', $_POST['StockCode'], '" />';
			echo '<br /></div>';
		}
		echo '<table>';
		echo '<tr>
				<th>', _('Code'), '</th>
				<th>', _('Description'), '</th>
				<th>', _('Units'), '</th>
			</tr>';
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($MyRow = DB_fetch_array($SearchResult)) and ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

			echo '<tr class="striped_row">
					<td><input type="submit" name="Select" value="', $MyRow['stockid'], '" /></td>
					<td>', $MyRow['description'], '</td>
					<td>', $MyRow['units'], '</td>
				</tr>';
			++$RowIndex;
			//end of page full new headings if
			
		}
		//end of while loop
		echo '</table>
			  </form>';
		echo '<a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SupplierID=', urlencode($_POST['SupplierID']), '">', _('Return to the main screen'), '</a>';
		include ('includes/footer.php');
		exit;
	}
}

foreach ($_POST as $Key => $Value) {
	if (mb_substr($Key, 0, 6) == 'Update') {
		$Index = mb_substr($Key, 6, mb_strlen($Key) - 6);
		$StockId = $_POST['StockID' . $Index];
		$Price = $_POST['Price' . $Index];
		$QtyGreaterThan = $_POST['QtyGreaterThan' . $Index];
		$OldQtyGreaterThan = $_POST['OldQtyGreaterThan' . $Index];
		$SuppUOM = $_POST['SuppUOM' . $Index];
		$ConversionFactor = $_POST['ConversionFactor' . $Index];
		$SupplierDescription = $_POST['SupplierDescription' . $Index];
		$LeadTime = $_POST['LeadTime' . $Index];
		if (isset($_POST['Preferred' . $Index])) {
			$Preferred = 1;
			$PreferredSQL = "UPDATE purchdata SET preferred=0
									WHERE stockid='" . $StockId . "'";
			$PreferredResult = DB_query($PreferredSQL);
		} else {
			$Preferred = 0;
		}
		$EffectiveFrom = $_POST['EffectiveFrom' . $Index];
		$SupplierPartNo = $_POST['SupplierPartNo' . $Index];
		$MinOrderQty = $_POST['MinOrderQty' . $Index];
		$SQL = "UPDATE purchdata SET price='" . $Price . "',
									qtygreaterthan='" . $QtyGreaterThan . "',
									suppliersuom='" . $SuppUOM . "',
									conversionfactor='" . $ConversionFactor . "',
									supplierdescription='" . $SupplierDescription . "',
									leadtime='" . $LeadTime . "',
									preferred='" . $Preferred . "',
									effectivefrom='" . FormatDateForSQL($EffectiveFrom) . "',
									suppliers_partno='" . $SupplierPartNo . "',
									minorderqty='" . $MinOrderQty . "'
								WHERE supplierno='" . DB_escape_string($_POST['SupplierID']) . "'
									AND stockid='" . $StockId . "'
									AND qtygreaterthan='" . $OldQtyGreaterThan . "'";
		$Result = DB_query($SQL);
	}
	if (mb_substr($Key, 0, 6) == 'Insert') {
		if (isset($_POST['Preferred0'])) {
			$Preferred = 1;
		} else {
			$Preferred = 0;
		}
		$SQL = "INSERT INTO purchdata (stockid,
									supplierno,
									price,
									suppliersuom,
									conversionfactor,
									supplierdescription,
									leadtime,
									preferred,
									effectivefrom,
									suppliers_partno,
									minorderqty
								) VALUES (
									'" . $_POST['StockID0'] . "',
									'" . DB_escape_string($_POST['SupplierID']) . "',
									'" . $_POST['Price0'] . "',
									'" . $_POST['SuppUOM0'] . "',
									'" . $_POST['ConversionFactor0'] . "',
									'" . $_POST['SupplierDescription0'] . "',
									'" . $_POST['LeadTime0'] . "',
									'" . $Preferred . "',
									'" . FormatDateForSQL($_POST['EffectiveFrom0']) . "',
									'" . $_POST['SupplierPartNo0'] . "',
									'" . $_POST['MinOrderQty0'] . "'
								)";
		$Result = DB_query($SQL);
	}
}

if (isset($_GET['SupplierID'])) {
	$SupplierID = stripslashes(trim(mb_strtoupper($_GET['SupplierID'])));
} elseif (isset($_POST['SupplierID'])) {
	$SupplierID = stripslashes(trim(mb_strtoupper($_POST['SupplierID'])));
}

if ((isset($SupplierID) and $SupplierID != '') and !isset($_POST['SearchSupplier'])) {
	/*NOT EDITING AN EXISTING BUT SUPPLIER selected or ENTERED*/
	$SQL = "SELECT suppliers.suppname, suppliers.currcode FROM suppliers WHERE supplierid='" . DB_escape_string($SupplierID) . "'";
	$ErrMsg = _('The supplier details for the selected supplier could not be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$SuppSelResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SuppSelResult) == 1) {
		$MyRow = DB_fetch_array($SuppSelResult);
		$SuppName = $MyRow['suppname'];
		$CurrCode = $MyRow['currcode'];
	} else {
		prnMsg(_('The supplier code') . ' ' . $SupplierID . ' ' . _('is not an existing supplier in the database') . '. ' . _('You must enter an alternative supplier code or select a supplier using the search facility below'), 'error');
		unset($SupplierID);
	}
} else {
	if ($NoPurchasingData = 0) {
		echo '<p class="page_title_text"><img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Search'), '" alt="" />', ' ', $Title, ' ', _('For Stock Code'), ' - ', $StockId, '</p>';
	}
	if (!isset($_POST['SearchSupplier'])) {
		echo '<p class="page_title_text"><img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Search'), '" alt="" />', _('Search for a supplier'), '</p>';
		echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
		echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

		echo '<fieldset>
				<legend>', _('Select a Supplier'), '</legend>
				<field>
					<label for="Keywords">', _('Text in the Supplier'), ' ', _('NAME'), ':</label>
					<input type="text" autofocus="autofocus" name="Keywords" size="20" maxlength="25" />
					<fieldhelp>', _('Enter a part, or all of a suppliers name'), '</fieldhelp>
				</field>
				<h2>', _('OR'), '</h2>
				<field>
					<label for="SupplierCode">', _('Text in Supplier'), ' ', _('CODE'), ':</label>
					<input type="text" name="SupplierCode" size="15" maxlength="18" />
					<fieldhelp>', _('Enter a part, or all of a suppliers code'), '</fieldhelp>
				</field>
			</fieldset>';
		echo '<div class="centre">
				<input type="submit" name="SearchSupplier" value="', _('Find Suppliers Now'), '" />
			</div>';
		echo '</form>';
		include ('includes/footer.php');
		exit;
	}
}

if (isset($_POST['SearchSupplier'])) {
	if ($_POST['Keywords'] == '' and $_POST['SupplierCode'] == '') {
		$_POST['Keywords'] = ' ';
	}
	if (mb_strlen($_POST['Keywords']) > 0) {
		//insert wildcard characters in spaces
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		$SQL = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.currcode,
					suppliers.address1,
					suppliers.address2,
					suppliers.address3
					FROM suppliers WHERE suppliers.suppname " . LIKE . " '" . $SearchString . "'";
	} elseif (mb_strlen($_POST['SupplierCode']) > 0) {
		$SQL = "SELECT suppliers.supplierid,
				suppliers.suppname,
				suppliers.currcode,
				suppliers.address1,
				suppliers.address2,
				suppliers.address3
			FROM suppliers
			WHERE suppliers.supplierid " . LIKE . " '%" . $_POST['SupplierCode'] . "%'";
	} //one of keywords or SupplierCode was more than a zero length string
	$ErrMsg = _('The suppliers matching the criteria entered could not be retrieved because');
	$DbgMsg = _('The SQL to retrieve supplier details that failed was');
	$SuppliersResult = DB_query($SQL, $ErrMsg, $DbgMsg);
} //end of if search
if (isset($SuppliersResult)) {
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Search'), '" alt="" />', _('Select a supplier'), '
		</p>';
	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	echo '<table cellpadding="2">
			<thead>
				<tr>
					<th class="SortedColumn">', _('Code'), '</th>
					<th class="SortedColumn">', _('Supplier Name'), '</th>
					<th>', _('Currency'), '</th>
					<th>', _('Address 1'), '</th>
					<th>', _('Address 2'), '</th>
					<th>', _('Address 3'), '</th>
				</tr>
			</thead>';
	$k = 0;
	echo '<tbody>';
	while ($MyRow = DB_fetch_array($SuppliersResult)) {
		echo '<tr class="striped_row">
				<td><input type="submit" name="SupplierID" value="', $MyRow['supplierid'], '" /></td>
				<td>', $MyRow['suppname'], '</td>
				<td>', $MyRow['currcode'], '</td>
				<td>', $MyRow['address1'], '</td>
				<td>', $MyRow['address2'], '</td>
				<td>', $MyRow['address3'], '</td>
			</tr>';

	}
	//end of while loop
	echo '</tbody>
	</table>
</form>';

	echo '<a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">', _('Return to supplier selection screen'), '</a><br />';

	include ('includes/footer.php');
	exit;
}
//end if results to show
if (isset($_POST['SupplierID'])) {
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Search'), '" alt="" />', _('Supplier Purchasing Data'), '
		</p>';
	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	$SQL = "SELECT purchdata.stockid,
				stockmaster.description,
				price,
				qtygreaterthan,
				suppliersuom,
				conversionfactor,
				supplierdescription,
				leadtime,
				preferred,
				effectivefrom,
				suppliers_partno,
				minorderqty,
				discontinued
			FROM purchdata
			INNER JOIN stockmaster
			ON purchdata.stockid=stockmaster.stockid
			WHERE supplierno='" . DB_escape_string($_POST['SupplierID']) . "'
			ORDER BY purchdata.stockid, effectivefrom DESC";

	$Result = DB_query($SQL);

	$UOMSQL = "SELECT unitid,
						unitname
					FROM unitsofmeasure";
	$UOMResult = DB_query($UOMSQL);
	echo '<input type="hidden" value="', stripslashes($_POST['SupplierID']), '" name="SupplierID" />';
	echo '<table>
			<tr>
				<th colspan="8" style="text-align: left"><h3>', _('Supplier purchasing data for'), ' ', stripslashes($_POST['SupplierID']), '</h3></th>
				<th colspan="5" style="text-align: right">', _('Find new Item Code'), '
					<button type="submit" name="StockSearch"><img width="15" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" alt="" /></button>
				</th>
			</tr>
			<tr>
				<th>', _('StockID'), '</th>
				<th>', _('Description'), '</th>
				<th>', _('Price'), '</th>
				<th>', _('Quantity Greater Than'), '</th>
				<th>', _('Suppliers UOM'), '</th>
				<th>', _('Conversion Factor'), '</th>
				<th>', _('Suppliers Description'), '</th>
				<th>', _('Lead Time'), '</th>
				<th>', _('Preferred'), '</th>
				<th>', _('Effective From'), '</th>
				<th>', _('Suppliers Item Code'), '</th>
				<th>', _('Min Order Qty'), '</th>
				<th>', _('Obsolete'), '</th>
				<th>', _('Save'), '</th>
 			</tr>';

	if (isset($_POST['Select'])) {
		$StockSQL = "SELECT description, units FROM stockmaster WHERE stockid='" . $_POST['Select'] . "'";
		$StockResult = DB_query($StockSQL);
		$StockRow = DB_fetch_array($StockResult);
		echo '<tr class="info_row">
				<td><input type="hidden" value="', $_POST['Select'], '" name="StockID0" />', $_POST['Select'], '</td>
				<td>', $StockRow['description'], '</td>
				<td><input type="text" class="number" required="required" maxlength="11" size="4" value="0.0000" name="Price0" /></td>
				<td><input type="text" class="number" required="required" maxlength="11" size="4" value="0.0000" name="QtyGreaterThan0" /></td>
				<td><select required="required" name="SuppUOM0">';
		while ($UOMRow = DB_fetch_array($UOMResult)) {
			if (isset($StRowoc['units']) and ($UOMRow['unitname'] == $StRowoc['units'])) {
				echo '<option selected="selected" value="', $UOMRow['unitname'], '">', $UOMRow['unitname'], '</option>';
			} else {
				echo '<option value="', $UOMRow['unitname'], '">', $UOMRow['unitname'], '</option>';
			}
		}
		DB_data_seek($UOMResult, 0);
		echo '</select></td>
				<td><input type="text" required="required" maxlength="11" class="number" size="4" value="1" name="ConversionFactor0" /></td>
				<td><input type="text" size="20" maxlength="50" value="" name="SupplierDescription0" /></td>
				<td><input type="text" class="number" required="required" maxlength="11" size="3" value="1" name="LeadTime0" /></td>';
		echo '<td><input type="checkbox" name="Preferred0" /></td>';
		echo '<td><input type="text" class="date" required="required" maxlength="10" size="6" value="', date($_SESSION['DefaultDateFormat']), '" name="EffectiveFrom0" /></td>
				<td><input type="text" size="20" maxlength="50" value="" name="SupplierPartNo0" /></td>
				<td><input type="text" class="number" required="required" maxlength="11" size="4" value="1" name="MinOrderQty0" /></td>
				<th>', 'N/A', '</th>
				<th><input type="submit" name="Insert" value="', _('Save'), '" /></th>
			</tr>';
	}

	$RowCounter = 1;
	while ($MyRow = DB_fetch_array($Result)) {
		if ($MyRow['discontinued'] == 1) {
			$Obsolete = _('Yes');
			$CSSClass = 'error_row';
		} else {
			$Obsolete = _('No');
			$CSSClass = 'striped_row';
		}
		echo '<tr class="', $CSSClass, '">
				<td><input type="hidden" value="', $MyRow['stockid'], '" name="StockID', $RowCounter, '" />', $MyRow['stockid'], '</td>
				<td>', $MyRow['description'], '</td>
				<td><input type="text" class="number" size="4" value="', locale_number_format($MyRow['price'], 4), '" name="Price', $RowCounter, '" /></td>
				<td><input type="text" class="number" size="4" value="', $MyRow['qtygreaterthan'], '" name="QtyGreaterThan', $RowCounter, '" /></td>
				<input type="hidden" value="', $MyRow['qtygreaterthan'], '" name="OldQtyGreaterThan', $RowCounter, '" />
				<td><select name="SuppUOM', $RowCounter, '">';
		DB_data_seek($UOMResult, 0);
		while ($UOMRow = DB_fetch_array($UOMResult)) {
			if ($UOMRow['unitname'] == $MyRow['suppliersuom']) {
				echo '<option selected="selected" value="', $UOMRow['unitname'], '">', $UOMRow['unitname'], '</option>';
			} else {
				echo '<option value="', $UOMRow['unitname'], '">', $UOMRow['unitname'], '</option>';
			}
		}
		echo '</select></td>
				<td><input type="text" class="number" size="4" value="', $MyRow['conversionfactor'], '" name="ConversionFactor', $RowCounter, '" /></td>
				<td><input type="text" size="20" maxlength="50" value="', $MyRow['supplierdescription'], '" name="SupplierDescription', $RowCounter, '" /></td>
				<td><input type="text" class="number" size="3" value="', $MyRow['leadtime'], '" name="LeadTime', $RowCounter, '" /></td>';
		if ($MyRow['preferred'] == 1) {
			echo '<td><input type="checkbox" checked="checked" name="Preferred', $RowCounter, '" /></td>';
		} else {
			echo '<td><input type="checkbox" name="Preferred', $RowCounter, '" /></td>';
		}
		echo '<td><input type="text" class="date" size="6" value="', ConvertSQLDate($MyRow['effectivefrom']), '" name="EffectiveFrom', $RowCounter, '" /></td>
				<td><input type="text" size="20" maxlength="50" value="', $MyRow['suppliers_partno'], '" name="SupplierPartNo', $RowCounter, '" /></td>
				<td><input type="text" class="number" size="4" value="', $MyRow['minorderqty'], '" name="MinOrderQty', $RowCounter, '" /></td>
				<th>', $Obsolete, '</th>
				<th><input type="submit" name="Update', $RowCounter, '" value="', _('Save'), '" /></th>
			</tr>';
		$RowCounter++;
	}
	echo '</table>';
	echo '</form>';
	include ('includes/footer.php');
	exit;
}

?>