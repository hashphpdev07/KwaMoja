<?php

include('includes/session.inc');

$Title = _('Supplier Maintenance');
/* Manual links before header.inc */
$ViewTopic = 'AccountsPayable';
$BookMark = 'NewSupplier';
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/CountriesArray.php');

function Is_ValidAccount($ActNo) {

	if (mb_strlen($ActNo) < 16) {
		echo _('NZ account numbers must have 16 numeric characters in it');
		return False;
	}

	if (!Is_double((double) $ActNo)) {
		echo _('NZ account numbers entered must use all numeric characters in it');
		return False;
	}

	$BankPrefix = mb_substr($ActNo, 0, 2);
	$BranchNumber = (int) (mb_substr($ActNo, 3, 4));

	if ($BankPrefix == '29') {
		echo _('NZ Accounts codes with the United Bank are not verified') . ', ' . _('be careful to enter the correct account number');
		exit;
	}

	//Verify correct branch details

	switch ($BankPrefix) {

		case '01':
			if (!(($BranchNumber >= 1 and $BranchNumber <= 999) or ($BranchNumber >= 1100 and $BranchNumber <= 1199))) {
				echo _('ANZ branches must be between 0001 and 0999 or between 1100 and 1199') . '. ' . _('The branch number used is invalid');
				return False;
			}
			break;
		case '02':
			if (!(($BranchNumber >= 1 and $BranchNumber <= 999) or ($BranchNumber >= 1200 and $BranchNumber <= 1299))) {
				echo _('Bank Of New Zealand branches must be between 0001 and 0999 or between 1200 and 1299') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;
		case '03':
			if (!(($BranchNumber >= 1 and $BranchNumber <= 999) or ($BranchNumber >= 1300 and $BranchNumber <= 1399))) {
				echo _('Westpac Trust branches must be between 0001 and 0999 or between 1300 and 1399') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '06':
			if (!(($BranchNumber >= 1 and $BranchNumber <= 999) or ($BranchNumber >= 1400 and $BranchNumber <= 1499))) {
				echo _('National Bank branches must be between 0001 and 0999 or between 1400 and 1499') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '08':
			if (!($BranchNumber >= 6500 and $BranchNumber <= 6599)) {
				echo _('National Australia branches must be between 6500 and 6599') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;
		case '09':
			if ($BranchNumber != 0) {
				echo _('The Reserve Bank branch should be 0000') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;
		case '12':

			//"13" "14" "15", "16", "17", "18", "19", "20", "21", "22", "23", "24":

			if (!($BranchNumber >= 3000 and $BranchNumber <= 4999)) {
				echo _('Trust Bank and Regional Bank branches must be between 3000 and 4999') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '11':
			if (!($BranchNumber >= 5000 and $BranchNumber <= 6499)) {
				echo _('Post Office Bank branches must be between 5000 and 6499') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '25':
			if (!($BranchNumber >= 2500 and $BranchNumber <= 2599)) {
				echo _('Countrywide Bank branches must be between 2500 and 2599') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;
		case '29':
			if (!($BranchNumber >= 2150 and $BranchNumber <= 2299)) {
				echo _('United Bank branches must be between 2150 and 2299') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '30':
			if (!($BranchNumber >= 2900 and $BranchNumber <= 2949)) {
				echo _('Hong Kong and Shanghai branches must be between 2900 and 2949') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '31':
			if (!($BranchNumber >= 2800 and $BranchNumber <= 2849)) {
				echo _('Citibank NA branches must be between 2800 and 2849') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		case '33':
			if (!($BranchNumber >= 6700 and $BranchNumber <= 6799)) {
				echo _('Rural Bank branches must be between 6700 and 6799') . '. ' . _('The branch number used is invalid');
				return False;
				exit;
			}
			break;

		default:
			echo _('The prefix') . ' - ' . $BankPrefix . ' ' . _('is not a valid New Zealand Bank') . '.<br />' . _('if you are outside New Zealand error trapping relevant to your country should be used');
			return False;
			exit;

	} // end of first Bank prefix switch

	for ($i = 3; $i <= 14; $i++) {

		$DigitVal = (double) (mb_substr($ActNo, $i, 1));

		switch ($i) {
			case 3:
				if ($BankPrefix == '08' or $BankPrefix == '09' or $BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = 0;
				} else {
					$CheckSum = $CheckSum + ($DigitVal * 6);
				}
				break;

			case 4:
				if ($BankPrefix == '08' or $BankPrefix == '09' or $BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = 0;
				} else {
					$CheckSum = $CheckSum + ($DigitVal * 3);
				}
				break;

			case 5:
				if ($BankPrefix == '08' or $BankPrefix == '09' or $BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = 0;
				} else {
					$CheckSum = $CheckSum + ($DigitVal * 7);
				}
				break;

			case 6:
				if ($BankPrefix == '08' or $BankPrefix == '09' or $BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = 0;
				} else {
					$CheckSum = $CheckSum + ($DigitVal * 9);
				}
				break;

			case 7:
				if ($BankPrefix == '08') {
					$CheckSum = $CheckSum + $DigitVal * 7;
				} elseif ($BankPrefix == '25' Or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal * 1;
				}
				break;

			case 8:
				if ($BankPrefix == '08') {
					$CheckSum = $CheckSum + ($DigitVal * 6);
				} elseif ($BankPrefix == '09') {
					$CheckSum = 0;
				} elseif ($BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal * 7;
				} else {
					$CheckSum = $CheckSum + $DigitVal * 10;
				}
				break;

			case 9:
				if ($BankPrefix == '09') {
					$CheckSum = 0;
				} elseif ($BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal * 3;
				} else {
					$CheckSum = $CheckSum + $DigitVal * 5;
				}
				break;

			case 10:
				if ($BankPrefix == '08') {
					$CheckSum = $CheckSum + $DigitVal * 4;
				} elseif ($BankPrefix == '09') {
					if (($DigitVal * 5) > 9) {
						$CheckSum = $CheckSum + (int) mb_substr((string) ($DigitVal * 5), 0, 1) + (int) mb_substr((string) ($DigitVal * 5), mb_strlen((string) ($DigitVal * 5)) - 1, 1);
					} else {
						$CheckSum = $CheckSum + $DigitVal * 5;
					}
				} elseif ($BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal;
				} else {
					$CheckSum = $CheckSum + $DigitVal * 8;
				}
				break;

			case 11:
				if ($BankPrefix == '08') {
					$CheckSum = $CheckSum + $DigitVal * 3;
				} elseif ($BankPrefix == '09') {
					if (($DigitVal * 4) > 9) {
						$CheckSum = $CheckSum + (int) mb_substr(($DigitVal * 4), 0, 1) + (int) mb_substr(($DigitVal * 4), mb_strlen($DigitVal * 4) - 1, 1);
					} else {
						$CheckSum = $CheckSum + $DigitVal * 4;
					}
				} elseif ($BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal * 7;
				} else {
					$CheckSum = $CheckSum + $DigitVal * 4;
				}
				break;

			case 12:
				if ($BankPrefix == '25' or $BankPrefix == '33') {
					$CheckSum = $CheckSum + $DigitVal * 3;
				} elseif ($BankPrefix == '09') {
					if (($DigitVal * 3) > 9) {
						$CheckSum = $CheckSum + (int) mb_substr(($DigitVal * 3), 0, 1) + (int) mb_substr(($DigitVal * 3), mb_strlen($DigitVal * 3) - 1, 1);
					} else {
						$CheckSum = $CheckSum + $DigitVal * 3;
					}
				} else {
					$CheckSum = $CheckSum + $DigitVal * 2;
				}
				break;

			case 13:
				if ($BankPrefix == '09') {
					if (($DigitVal * 2) > 9) {
						$CheckSum = $CheckSum + (int) mb_substr(($DigitVal * 2), 0, 1) + (int) mb_substr(($DigitVal * 2), mb_strlen($DigitVal * 2) - 1, 1);
					} else {
						$CheckSum = $CheckSum + $DigitVal * 2;
					}
				} else {
					$CheckSum = $CheckSum + $DigitVal;
				}
				break;

			case 14:
				if ($BankPrefix == '09') {
					$CheckSum = $CheckSum + $DigitVal;
				}
				break;
		} //end switch

	} //end for loop

	if ($BankPrefix == '25' or $BankPrefix == '33') {
		if ($CheckSum / 10 - (int) ($CheckSum / 10) != 0) {
			echo '<p>' . _('The account number entered does not meet the banking check sum requirement and cannot be a valid account number');
			return False;
		}
	} else {
		if ($CheckSum / 11 - (int) ($CheckSum / 11) != 0) {
			echo '<p>' . _('The account number entered does not meet the banking check sum requirement and cannot be a valid account number');
			return False;
		}
	}

} //End function


if (isset($_GET['SupplierID'])) {
	$SupplierID = mb_strtoupper(stripslashes($_GET['SupplierID']));
} elseif (isset($_POST['SupplierID'])) {
	$SupplierID = mb_strtoupper(stripslashes($_POST['SupplierID']));
} else {
	unset($SupplierID);
}

echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/supplier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Suppliers') . '</p>';

if (isset($SupplierID)) {
	echo '<p>
			<a href="' . $RootPath . '/SupplierContacts.php?SupplierID=' . urlencode(stripslashes($SupplierID)) . '">' . _('Review Supplier Contact Details') . '</a>
		</p>';
}

$InputError = 0;

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$i = 1;
	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$SQL = "SELECT COUNT(supplierid) FROM suppliers WHERE supplierid='" . DB_escape_string($SupplierID) . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0 and isset($_POST['New'])) {
		$InputError = 1;
		prnMsg(_('The supplier number already exists in the database'), 'error');
		$Errors[$i] = 'ID';
		++$i;
	}
	if (mb_strlen(trim($_POST['SuppName'])) > 40 or mb_strlen(trim($_POST['SuppName'])) == 0 or trim($_POST['SuppName']) == '') {
		$InputError = 1;
		prnMsg(_('The supplier name must be entered and be forty characters or less long'), 'error');
		$Errors[$i] = 'Name';
		++$i;
	}
	if ($_SESSION['AutoSupplierNo'] == 0 and mb_strlen($SupplierID) == 0) {
		$InputError = 1;
		prnMsg(_('The Supplier Code cannot be empty'), 'error');
		$Errors[$i] = 'ID';
		++$i;
	}
	//	if (ContainsIllegalCharacters($SupplierID)) {
	//		$InputError = 1;
	//		prnMsg(_('The supplier code cannot contain any of the illegal characters') ,'error');
	//		$Errors[$i]='ID';
	//		++$i;
	//	}
	if (mb_strlen($_POST['Phone']) > 25) {
		$InputError = 1;
		prnMsg(_('The telephone number must be 25 characters or less long'), 'error');
		$Errors[$i] = 'Telephone';
		++$i;
	}
	if (mb_strlen($_POST['Fax']) > 25) {
		$InputError = 1;
		prnMsg(_('The fax number must be 25 characters or less long'), 'error');
		$Errors[$i] = 'Fax';
		++$i;
	}
	if (mb_strlen($_POST['Email']) > 55) {
		$InputError = 1;
		prnMsg(_('The email address must be 55 characters or less long'), 'error');
		$Errors[$i] = 'Email';
		++$i;
	}
	if (mb_strlen($_POST['Email']) > 0 and !IsEmailAddress($_POST['Email'])) {
		$InputError = 1;
		prnMsg(_('The email address is not correctly formed'), 'error');
		$Errors[$i] = 'Email';
		++$i;
	}
	if (mb_strlen($_POST['URL']) >50) {
		$InputError = 1;
		prnMsg(_('The URL address must be 50 characters or less long'),'error');
		$Errors[$i] = 'URL';
		++$i;
	}
	if (mb_strlen($_POST['BankRef']) > 12) {
		$InputError = 1;
		prnMsg(_('The bank reference text must be less than 12 characters long'), 'error');
		$Errors[$i] = 'BankRef';
		++$i;
	}
	if (!is_date($_POST['SupplierSince'])) {
		$InputError = 1;
		prnMsg(_('The supplier since field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'], 'error');
		$Errors[$i] = 'SupplierSince';
		++$i;
	}

	/*
	elseif (mb_strlen($_POST['BankAct']) > 1 ) {
	if (!Is_ValidAccount($_POST['BankAct'])) {
	prnMsg(_('The bank account entry is not a valid New Zealand bank account number. This is (of course) no concern if the business operates outside of New Zealand'),'warn');
	}
	}
	*/

	if ($InputError != 1) {

		$SQL_SupplierSince = FormatDateForSQL($_POST['SupplierSince']);

		$latitude = 0;
		$longitude = 0;
		if ($_SESSION['geocode_integration'] == 1) {
			// Get the lat/long from our geocoding host
			$SQL = "SELECT * FROM geocode_param WHERE 1";
			$ErrMsg = _('An error occurred in retrieving the information');
			$Resultgeo = DB_query($SQL, $ErrMsg);
			$row = DB_fetch_array($Resultgeo);
			$api_key = $row['geocode_key'];
			$map_host = $row['map_host'];
			define('MAPS_HOST', $map_host);
			define('KEY', $api_key);
			// check that some sane values are setup already in geocode tables, if not skip the geocoding but add the record anyway.
			if ($map_host == "") {
				echo '<div class="warn">' . _('Warning - Geocode Integration is enabled, but no hosts are setup.  Go to Geocode Setup') . '</div>';
			} else {
				$address = $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . ', ' . $_POST['Address6'];

				$base_url = 'http://' . MAPS_HOST . '/maps/geo?output=xml' . '&key=' . KEY;
				$request_url = $base_url . '&q=' . urlencode($address);

				$xml = simplexml_load_string(utf8_encode(file_get_contents($request_url))) or prnMsg(_('Goole map url not loading'),'warn');
				//			$xml = simplexml_load_file($request_url) or die("url not loading");

				$coordinates = $xml->Response->Placemark->Point->coordinates;
				$coordinatesSplit = explode(',', $coordinates);
				// Format: Longitude, Latitude, Altitude
				$latitude = $coordinatesSplit[1];
				$longitude = $coordinatesSplit[0];

				$status = $xml->Response->Status->code;
				if (strcmp($status, '200') == 0) {
					// Successful geocode
					$geocode_pending = false;
					$coordinates = $xml->Response->Placemark->Point->coordinates;
					$coordinatesSplit = explode(",", $coordinates);
					// Format: Longitude, Latitude, Altitude
					$latitude = $coordinatesSplit[1];
					$longitude = $coordinatesSplit[0];
				} else {
					// failure to geocode
					$geocode_pending = false;
					echo '<p>' . _('Address') . ': ' . $address . ' ' . _('failed to geocode') . "\n";
					echo _('Received status') . ' ' . $status . "\n" . '</p>';
				}
			}
		}
		if (!isset($_POST['New'])) {

			$supptranssql = "SELECT supplierno
							FROM supptrans
							WHERE supplierno='" . DB_escape_string($SupplierID) . "'";
			$suppresult = DB_query($supptranssql);
			$supptrans = DB_num_rows($suppresult);

			$suppcurrssql = "SELECT currcode
							FROM suppliers
							WHERE supplierid='" . DB_escape_string($SupplierID) . "'";
			$currresult = DB_query($suppcurrssql);
			$suppcurr = DB_fetch_row($currresult);

			if ($supptrans == 0) {
				$SQL = "UPDATE suppliers SET suppname='" . $_POST['SuppName'] . "',
							address1='" . $_POST['Address1'] . "',
							address2='" . $_POST['Address2'] . "',
							address3='" . $_POST['Address3'] . "',
							address4='" . $_POST['Address4'] . "',
							address5='" . $_POST['Address5'] . "',
							address6='" . $_POST['Address6'] . "',
							telephone='" . $_POST['Phone'] . "',
							fax='" . $_POST['Fax'] . "',
							email='" . $_POST['Email'] . "',
							url='" . $_POST['URL'] . "',
							supptype='" . $_POST['SupplierType'] . "',
							currcode='" . $_POST['CurrCode'] . "',
							suppliersince='" . $SQL_SupplierSince . "',
							paymentterms='" . $_POST['PaymentTerms'] . "',
							bankpartics='" . $_POST['BankPartics'] . "',
							bankref='" . $_POST['BankRef'] . "',
					 		bankact='" . $_POST['BankAct'] . "',
							remittance='" . $_POST['Remittance'] . "',
							taxgroupid='" . $_POST['TaxGroup'] . "',
							factorcompanyid='" . $_POST['FactorID'] . "',
							lat='" . $latitude . "',
							lng='" . $longitude . "',
							taxref='" . $_POST['TaxRef'] . "'
						WHERE supplierid = '" . DB_escape_string($SupplierID) . "'";
			} else {
				if ($suppcurr[0] != $_POST['CurrCode']) {
					prnMsg(_('Cannot change currency code as transactions already exist'), 'info');
				}
				$SQL = "UPDATE suppliers SET suppname='" . $_POST['SuppName'] . "',
							address1='" . $_POST['Address1'] . "',
							address2='" . $_POST['Address2'] . "',
							address3='" . $_POST['Address3'] . "',
							address4='" . $_POST['Address4'] . "',
							address5='" . $_POST['Address5'] . "',
							address6='" . $_POST['Address6'] . "',
							telephone='" . $_POST['Phone'] . "',
							fax='" . $_POST['Fax'] . "',
							email='" . $_POST['Email'] . "',
							url='" . $_POST['URL'] . "',
							supptype='" . $_POST['SupplierType'] . "',
							suppliersince='" . $SQL_SupplierSince . "',
							paymentterms='" . $_POST['PaymentTerms'] . "',
							bankpartics='" . $_POST['BankPartics'] . "',
							bankref='" . $_POST['BankRef'] . "',
					 		bankact='" . $_POST['BankAct'] . "',
							remittance='" . $_POST['Remittance'] . "',
							taxgroupid='" . $_POST['TaxGroup'] . "',
							factorcompanyid='" . $_POST['FactorID'] . "',
							lat='" . $latitude . "',
							lng='" . $longitude . "',
							taxref='" . $_POST['TaxRef'] . "'
						WHERE supplierid = '" . DB_escape_string($SupplierID) . "'";
			}

			$ErrMsg = _('The supplier could not be updated because');
			$DbgMsg = _('The SQL that was used to update the supplier but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('The supplier master record for') . ' ' . $SupplierID . ' ' . _('has been updated'), 'success');

		} else { //its a new supplier

			if ($_SESSION['AutoSupplierNo'] == 1) {
				/* system assigned, sequential, numeric */
				$SupplierID = GetNextTransNo(600);
			}

			$SQL = "INSERT INTO suppliers (supplierid,
										suppname,
										address1,
										address2,
										address3,
										address4,
										address5,
										address6,
										telephone,
										fax,
										email,
										url,
										supptype,
										currcode,
										suppliersince,
										paymentterms,
										bankpartics,
										bankref,
										bankact,
										remittance,
										taxgroupid,
										factorcompanyid,
										lat,
										lng,
										taxref)
								 VALUES ('" . DB_escape_string($SupplierID) . "',
								 	'" . $_POST['SuppName'] . "',
									'" . $_POST['Address1'] . "',
									'" . $_POST['Address2'] . "',
									'" . $_POST['Address3'] . "',
									'" . $_POST['Address4'] . "',
									'" . $_POST['Address5'] . "',
									'" . $_POST['Address6'] . "',
									'" . $_POST['Phone'] . "',
									'" . $_POST['Fax'] . "',
									'" . $_POST['Email'] . "',
									'" . $_POST['URL'] . "',
									'" . $_POST['SupplierType'] . "',
									'" . $_POST['CurrCode'] . "',
									'" . $SQL_SupplierSince . "',
									'" . $_POST['PaymentTerms'] . "',
									'" . $_POST['BankPartics'] . "',
									'" . $_POST['BankRef'] . "',
									'" . $_POST['BankAct'] . "',
									'" . $_POST['Remittance'] . "',
									'" . $_POST['TaxGroup'] . "',
									'" . $_POST['FactorID'] . "',
									'" . $latitude . "',
									'" . $longitude . "',
									'" . $_POST['TaxRef'] . "')";

			$ErrMsg = _('The supplier') . ' ' . $_POST['SuppName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the supplier but failed was');

			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new supplier for') . ' ' . $_POST['SuppName'] . ' ' . _('has been added to the database'), 'success');

			echo '<p>
					<a href="' . $RootPath . '/SupplierContacts.php?SupplierID=' . urlencode($SupplierID) . '">' . _('Review Supplier Contact Details') . '</a>
				  </p>';

			unset($SupplierID);
			unset($_POST['SuppName']);
			unset($_POST['Address1']);
			unset($_POST['Address2']);
			unset($_POST['Address3']);
			unset($_POST['Address4']);
			unset($_POST['Address5']);
			unset($_POST['Address6']);
			unset($_POST['Phone']);
			unset($_POST['Fax']);
			unset($_POST['Email']);
			unset($_POST['URL']);
			unset($_POST['SupplierType']);
			unset($_POST['CurrCode']);
			unset($SQL_SupplierSince);
			unset($_POST['PaymentTerms']);
			unset($_POST['BankPartics']);
			unset($_POST['BankRef']);
			unset($_POST['BankAct']);
			unset($_POST['Remittance']);
			unset($_POST['TaxGroup']);
			unset($_POST['FactorID']);
			unset($_POST['TaxRef']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {

	//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts

	$SQL = "SELECT COUNT(*) FROM supptrans WHERE supplierno='" . $SupplierID . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this supplier because there are transactions that refer to this supplier'), 'warn');
		echo '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('transactions against this supplier');

	} else {
		$SQL = "SELECT COUNT(*) FROM purchorders WHERE supplierno='" . $SupplierID . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			$CancelDelete = 1;
			prnMsg(_('Cannot delete the supplier record because purchase orders have been created against this supplier'), 'warn');
			echo '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('orders against this supplier');
		} else {
			$SQL = "SELECT COUNT(*) FROM suppliercontacts WHERE supplierid='" . $SupplierID . "'";
			$Result = DB_query($SQL);
			$MyRow = DB_fetch_row($Result);
			if ($MyRow[0] > 0) {
				$CancelDelete = 1;
				prnMsg(_('Cannot delete this supplier because there are supplier contacts set up against it') . ' - ' . _('delete these first'), 'warn');
				echo '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('supplier contacts relating to this supplier');

			}
		}

	}
	if ($CancelDelete == 0) {
		$SQL = "DELETE FROM suppliers WHERE supplierid='" . $SupplierID . "'";
		$Result = DB_query($SQL);
		prnMsg(_('Supplier record for') . ' ' . $SupplierID . ' ' . _('has been deleted'), 'success');
		unset($SupplierID);
		unset($_SESSION['SupplierID']);
	} //end if Delete supplier
}


if (!isset($SupplierID)) {

	/*if the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="New" value="Yes" />';

	echo '<table class="selection">';

	/* if $AutoSupplierNo is off (not 0) then provide an input box for the SupplierID to manually assigned */
	if ($_SESSION['AutoSupplierNo'] == 0)  {
		echo '<tr>
				<td>' . _('Supplier Code') . ':</td>
				<td><input type="text" title="' . _('The supplier id should not be within 10 legal characters and cannot be blank') . '" required="required" name="SupplierID" size="11" maxlength="10" /></td>
			</tr>';
	}
	echo '<tr>
			<td>' . _('Supplier Name') . ':</td>
			<td><input type="text" name="SuppName" size="42" required="required" minlength="1" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 1 (Street)') . ':</td>
			<td><input type="text" name="Address1" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 2 (Street)') . ':</td>
			<td><input type="text" name="Address2" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 3 (Suburb/City)') . ':</td>
			<td><input type="text" name="Address3" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 4 (State/Province)') . ':</td>
			<td><input type="text" name="Address4" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 5 (Postal Code)') . ':</td>
			<td><input type="text" name="Address5" size="42" minlength="0" maxlength="40" /></td>
		</tr>';

	echo '<tr>
			<td>' . _('Country') . ':</td>
			<td><select minlength="0" name="Address6">';
	foreach ($CountriesArray as $CountryEntry => $CountryName) {
		if (isset($_POST['Address6']) and ($_POST['Address6'] == $CountryName)) {
			echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
		} elseif (!isset($_POST['Address6']) and $CountryName == "") {
			echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
		} else {
			echo '<option value="' . $CountryName . '">' . $CountryName . '</option>';
		}
	}
	echo '</select></td>
		</tr>';

	echo '<tr>
			<td>' . _('Telephone') . ':</td>
			<td><input type="tel" name="Phone" size="30" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Facsimile') . ':</td>
			<td><input type="tel" name="Fax" size="30" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Email Address') . ':</td>
			<td><input type="email" name="Email" size="30" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('URL') . ':</td>
			<td><input type="url" name="URL" size="30" minlength="0" maxlength="50" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Supplier Type') . ':</td>
			<td><select minlength="0" name="SupplierType">';
	$Result = DB_query("SELECT typeid, typename FROM suppliertype");
	while ($MyRow = DB_fetch_array($Result)) {
		echo '<option value="' . $MyRow['typeid'] . '">' . $MyRow['typename'] . '</option>';
	} //end while loop
	echo '</select></td></tr>';

	$DateString = Date($_SESSION['DefaultDateFormat']);
	echo '<tr>
			<td>' . _('Supplier Since') . ' (' . $_SESSION['DefaultDateFormat'] . '):</td>
			<td><input type="text" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" name="SupplierSince" value="' . $DateString . '" size="12" required="required" minlength="1" maxlength="10" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank Particulars') . ':</td>
			<td><input type="text" name="BankPartics" size="13" minlength="0" maxlength="12" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank reference') . ':</td>
			<td><input type="text" name="BankRef" value="0" size="13" minlength="0" maxlength="12" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank Account No') . ':</td>
			<td><input type="text" name="BankAct" size="31" minlength="0" maxlength="30" /></td>
		</tr>';

	$Result = DB_query("SELECT terms, termsindicator FROM paymentterms");

	echo '<tr>
			<td>' . _('Payment Terms') . ':</td>
			<td><select required="required" minlength="1" name="PaymentTerms">';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<option value="' . $MyRow['termsindicator'] . '">' . $MyRow['terms'] . '</option>';
	} //end while loop
	echo '</select></td></tr>';

	$Result = DB_query("SELECT id, coyname FROM factorcompanies");

	echo '<tr>
			<td>' . _('Factor Company') . ':</td>
			<td><select required="required" minlength="1" name="FactorID">';
	echo '<option value="0">' . _('None') . '</option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($_POST['FactorID']) and $_POST['FactorID'] == $MyRow['id']) {
			echo '<option selected="selected" value="' . $MyRow['id'] . '">' . $MyRow['coyname'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['id'] . '">' . $MyRow['coyname'] . '</option>';
		}
	} //end while loop
	echo '</select></td></tr>';
	echo '<tr>
			<td>' . _('Tax Reference') . ':</td>
			<td><input type="text" name="TaxRef" size="21" minlength="0" maxlength="20" /></td></tr>';

	$Result = DB_query("SELECT currency, currabrev FROM currencies");
	if (!isset($_POST['CurrCode'])) {
		$CurrResult = DB_query("SELECT currencydefault FROM companies WHERE coycode=1");
		$MyRow = DB_fetch_row($CurrResult);
		$_POST['CurrCode'] = $MyRow[0];
	}

	echo '<tr>
			<td>' . _('Supplier Currency') . ':</td>
			<td><select required="required" minlength="1" name="CurrCode">';
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['CurrCode'] == $MyRow['currabrev']) {
			echo '<option selected="selected" value="' . $MyRow['currabrev'] . '">' . $MyRow['currency'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['currabrev'] . '">' . $MyRow['currency'] . '</option>';
		}
	} //end while loop

	echo '</select></td></tr>
			<tr>
				<td>' . _('Remittance Advice') . ':</td>
				<td><select required="required" minlength="1" name="Remittance">';
	echo '<option value="0">' . _('Not Required') . '</option>';
	echo '<option value="1">' . _('Required') . '</option>';

	echo '</select></td></tr>';

	echo '<tr>
			<td>' . _('Tax Group') . ':</td>
			<td><select required="required" minlength="1" name="TaxGroup">';


	$SQL = "SELECT taxgroupid, taxgroupdescription FROM taxgroups";
	$Result = DB_query($SQL);

	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($_POST['TaxGroup']) and $_POST['TaxGroup'] == $MyRow['taxgroupid']) {
			echo '<option selected="selected" value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
		}
	} //end while loop

	echo '</select></td></tr>
		</table>
		<div class="centre"><input type="submit" name="submit" value="' . _('Insert New Supplier') . '" /></div>';
	echo '</form>';

} else {

	//SupplierID exists - either passed when calling the form or from the form itself

	echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';

	if (!isset($_POST['New'])) {
		$SQL = "SELECT supplierid,
						suppname,
						address1,
						address2,
						address3,
						address4,
						address5,
						address6,
						telephone,
						fax,
						email,
						url,
						supptype,
						currcode,
						suppliersince,
						paymentterms,
						bankpartics,
						bankref,
						bankact,
						remittance,
						taxgroupid,
						factorcompanyid,
						taxref
					FROM suppliers
					WHERE supplierid = '" . DB_escape_string($SupplierID) . "'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['SuppName'] = stripcslashes($MyRow['suppname']);
		$_POST['Address1'] = stripcslashes($MyRow['address1']);
		$_POST['Address2'] = stripcslashes($MyRow['address2']);
		$_POST['Address3'] = stripcslashes($MyRow['address3']);
		$_POST['Address4'] = stripcslashes($MyRow['address4']);
		$_POST['Address5'] = stripcslashes($MyRow['address5']);
		$_POST['Address6'] = stripcslashes($MyRow['address6']);
		$_POST['CurrCode'] = stripcslashes($MyRow['currcode']);
		$_POST['Phone'] = $MyRow['telephone'];
		$_POST['Fax'] = $MyRow['fax'];
		$_POST['Email'] = $MyRow['email'];
		$_POST['URL'] = $MyRow['url'];
		$_POST['SupplierType'] = $MyRow['supptype'];
		$_POST['SupplierSince'] = ConvertSQLDate($MyRow['suppliersince']);
		$_POST['PaymentTerms'] = $MyRow['paymentterms'];
		$_POST['BankPartics'] = stripcslashes($MyRow['bankpartics']);
		$_POST['Remittance'] = $MyRow['remittance'];
		$_POST['BankRef'] = stripcslashes($MyRow['bankref']);
		$_POST['BankAct'] = $MyRow['bankact'];
		$_POST['TaxGroup'] = $MyRow['taxgroupid'];
		$_POST['FactorID'] = $MyRow['factorcompanyid'];
		$_POST['TaxRef'] = $MyRow['taxref'];

		echo '<tr><td><input type="hidden" name="SupplierID" value="' . $SupplierID . '" /></td></tr>';

	} else {
		/* if $AutoSupplierNo is off (i.e. 0) then provide an input box for the SupplierID to manually assigned */
		echo '<input type="hidden" name="New" value="Yes" />';
		if ($_SESSION['AutoSupplierNo'] == 0)  {
		// its a new supplier being added
			echo '<tr>
					<td>' . _('Supplier Code') . ':</td>
					<td><input type="text" name="SupplierID" value="' . $SupplierID . '" size="12" maxlength="10" /></td>
				</tr>';
		}
	}

	echo '<tr>
			<td>' . _('Supplier Name') . ':</td>
			<td><input type="text" name="SuppName" value="' . $_POST['SuppName'] . '" size="42" required="required" minlength="1" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 1 (Street)') . ':</td>
			<td><input type="text" name="Address1" value="' . $_POST['Address1'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 2 (Street)') . ':</td>
			<td><input type="text" name="Address2" value="' . $_POST['Address2'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 3 (Suburb/City)') . ':</td>
			<td><input type="text" name="Address3" value="' . $_POST['Address3'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 4 (State/Province)') . ':</td>
			<td><input type="text" name="Address4" value="' . $_POST['Address4'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Address Line 5 (Postal Code)') . ':</td>
			<td><input type="text" name="Address5" value="' . $_POST['Address5'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';

	echo '<tr>
			<td>' . _('Country') . ':</td>
			<td><select required="required" minlength="1" name="Address6">';
	foreach ($CountriesArray as $CountryEntry => $CountryName) {
		if (isset($_POST['Address6']) and ($_POST['Address6'] == $CountryName)) {
			echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
		} elseif (!isset($_POST['Address6']) and $CountryName == "") {
			echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
		} else {
			echo '<option value="' . $CountryName . '">' . $CountryName . '</option>';
		}
	}
	echo '</select></td>
		</tr>';

	echo '<tr>
			<td>' . _('Telephone') . ':</td>
			<td><input type="tel" name="Phone" value="' . $_POST['Phone'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Facsimile') . ':</td>
			<td><input  type="tel" name="Fax" value="' . $_POST['Fax'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Email Address') . ':</td>
			<td><input type="email" name="Email" value="' . $_POST['Email'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('URL') . ':</td>
			<td><input type="url" name="URL" value="' . $_POST['URL'] . '" size="42" minlength="0" maxlength="40" /></td>
		</tr>';

	echo '<tr>
			<td>' . _('Supplier Type') . ':</td>
			<td><select required="required" minlength="1" name="SupplierType">';
	$Result = DB_query("SELECT typeid, typename FROM suppliertype");
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['SupplierType'] == $MyRow['typeid']) {
			echo '<option selected="selected" value="' . $MyRow['typeid'] . '">' . $MyRow['typename'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['typeid'] . '">' . $MyRow['typename'] . '</option>';
		}
	} //end while loop
	echo '</select></td></tr>';

	echo '<tr>
			<td>' . _('Supplier Since') . ' (' . $_SESSION['DefaultDateFormat'] . '):</td>
			<td><input size="12" required="required" minlength="1" maxlength="10" type="text" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" name="SupplierSince" value="' . $_POST['SupplierSince'] . '" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank Particulars') . ':</td>
			<td><input type="text" name="BankPartics" size="13" minlength="0" maxlength="12" value="' . $_POST['BankPartics'] . '" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank Reference') . ':</td>
			<td><input type="text" name="BankRef" size="13" minlength="0" maxlength="12" value="' . $_POST['BankRef'] . '" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Bank Account No') . ':</td>
			<td><input type="text" name="BankAct" size="31" minlength="0" maxlength="30" value="' . $_POST['BankAct'] . '" /></td>
		</tr>';

	$Result = DB_query("SELECT terms, termsindicator FROM paymentterms");

	echo '<tr>
			<td>' . _('Payment Terms') . ':</td>
			<td><select required="required" minlength="1" name="PaymentTerms">';

	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['PaymentTerms'] == $MyRow['termsindicator']) {
			echo '<option selected="selected" value="' . $MyRow['termsindicator'] . '">' . $MyRow['terms'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['termsindicator'] . '">' . $MyRow['terms'] . '</option>';
		}
	} //end while loop
	echo '</select></td></tr>';

	$Result = DB_query("SELECT id, coyname FROM factorcompanies");

	echo '<tr>
			<td>' . _('Factor Company') . ':</td>
			<td><select minlength="0" name="FactorID">';
	echo '<option value="0">' . _('None') . '</option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['FactorID'] == $MyRow['id']) {
			echo '<option selected="selected" value="' . $MyRow['id'] . '">' . $MyRow['coyname'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['id'] . '">' . $MyRow['coyname'] . '</option>';
		}
	} //end while loop
	echo '</select></td></tr>';
	echo '<tr>
			<td>' . _('Tax Reference') . ':</td>
			<td><input type="text" name="TaxRef" size="21" minlength="0" maxlength="20" value="' . $_POST['TaxRef'] . '" /></td></tr>';

	$Result = DB_query("SELECT currency, currabrev FROM currencies");

	echo '<tr><td>' . _('Supplier Currency') . ':</td>
			<td><select minlength="0" name="CurrCode">';
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['CurrCode'] == $MyRow['currabrev']) {
			echo '<option selected="selected" value="' . $MyRow['currabrev'] . '">' . $MyRow['currency'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['currabrev'] . '">' . $MyRow['currency'] . '</option>';
		}
	} //end while loop

	echo '</select></td></tr>
		<tr><td>' . _('Remittance Advice') . ':</td>
			<td><select minlength="0" name="Remittance">';

	if ($_POST['Remittance'] == 0) {
		echo '<option selected="selected" value="0">' . _('Not Required') . '</option>';
		echo '<option value="1">' . _('Required') . '</option>';
	} else {
		echo '<option value="0">' . _('Not Required') . '</option>';
		echo '<option selected="selected" value="1">' . _('Required') . '</option>';

	}

	echo '</select>
				</td>
			</tr>';

	echo '<tr>
			<td>' . _('Tax Group') . ':</td>
			<td><select minlength="0" name="TaxGroup">';

	$SQL = "SELECT taxgroupid, taxgroupdescription FROM taxgroups";
	$Result = DB_query($SQL);

	while ($MyRow = DB_fetch_array($Result)) {
		if ($MyRow['taxgroupid'] == $_POST['TaxGroup']) {
			echo '<option selected="selected" value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
		}

	} //end while loop

	echo '</select>
				</td>
			</tr>
		</table>';

	if (isset($_POST['New'])) {
		echo '<div class="centre">
					 <input type="submit" name="submit" value="' . _('Add These New Supplier Details') . '" />
				</div>';
	} else {
		echo '<div class="centre">
					<input type="submit" name="submit" value="' . _('Update Supplier') . '" />
				</div>';
		prnMsg( _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed'), 'Warn');
		echo '<div class="centre">
				<input type="submit" name="delete" value="' . _('Delete Supplier') . '" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this supplier?') . '\');" />
			</div>';
	}
	echo '</form>';
} // end of main ifs

include('includes/footer.inc');
?>