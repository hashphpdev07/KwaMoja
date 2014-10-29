<?php

/* $Id$ */

include('includes/session.inc');

$Title = _('Create or Modify Insurance Company Details');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/CountriesArray.php');

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Customer') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($Errors)) {
	unset($Errors);
}

if (isset($_GET['Debtor'])) {
	$_POST['DebtorNo'] = $_GET['Debtor'];
}

$Errors = array();

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i = 1;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST['DebtorNo'] = strtoupper($_POST['DebtorNo']);

	$SQL = "SELECT COUNT(debtorno) FROM debtorsmaster WHERE debtorno='" . $_POST['DebtorNo'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0 and isset($_POST['New'])) {
		$InputError = 1;
		prnMsg(_('The company number already exists in the database'), 'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	} elseif (strlen($_POST['CustName']) > 40 OR strlen($_POST['CustName']) == 0) {
		$InputError = 1;
		prnMsg(_('The company name must be entered and be forty characters or less long'), 'error');
		$Errors[$i] = 'CustName';
		$i++;
	} elseif (strlen($_POST['DebtorNo']) == 0) {
		$InputError = 1;
		prnMsg(_('The debtor code cannot be empty'), 'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	} elseif ((ContainsIllegalCharacters($_POST['DebtorNo']) OR strpos($_POST['DebtorNo'], ' '))) {
		$InputError = 1;
		prnMsg(_('The customer code cannot contain any of the illefal characters'), 'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	} elseif (strlen($_POST['Address1']) > 40) {
		$InputError = 1;
		prnMsg(_('The Line 1 of the address must be forty characters or less long'), 'error');
		$Errors[$i] = 'Address1';
		$i++;
	} elseif (strlen($_POST['Address2']) > 40) {
		$InputError = 1;
		prnMsg(_('The Line 2 of the address must be forty characters or less long'), 'error');
		$Errors[$i] = 'Address2';
		$i++;
	} elseif (strlen($_POST['Address3']) > 40) {
		$InputError = 1;
		prnMsg(_('The Line 3 of the address must be forty characters or less long'), 'error');
		$Errors[$i] = 'Address3';
		$i++;
	} elseif (strlen($_POST['Address4']) > 50) {
		$InputError = 1;
		prnMsg(_('The Line 4 of the address must be fifty characters or less long'), 'error');
		$Errors[$i] = 'Address4';
		$i++;
	} elseif (strlen($_POST['Address5']) > 20) {
		$InputError = 1;
		prnMsg(_('The Line 5 of the address must be twenty characters or less long'), 'error');
		$Errors[$i] = 'Address5';
		$i++;
	} elseif (strlen($_POST['Address6']) > 15) {
		$InputError = 1;
		prnMsg(_('The Line 6 of the address must be fifteen characters or less long'), 'error');
		$Errors[$i] = 'Address6';
		$i++;
	} elseif (strlen($_POST['Phone']) > 25) {
		$InputError = 1;
		prnMsg(_('The telephone number must be 25 characters or less long'), 'error');
		$Errors[$i] = 'Telephone';
		$i++;
	} elseif (strlen($_POST['Fax']) > 25) {
		$InputError = 1;
		prnMsg(_('The fax number must be 25 characters or less long'), 'error');
		$Errors[$i] = 'Fax';
		$i++;
	} elseif (strlen($_POST['Email']) > 55) {
		$InputError = 1;
		prnMsg(_('The email address must be 55 characters or less long'), 'error');
		$Errors[$i] = 'Email';
		$i++;
	} elseif (strlen($_POST['Email']) > 0 and !IsEmailAddress($_POST['Email'])) {
		$InputError = 1;
		prnMsg(_('The email address is not correctly formed'), 'error');
		$Errors[$i] = 'Email';
		$i++;
	}

	if ($InputError != 1) {

		$SQL = "SELECT typeabbrev FROM salestypes";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);
		$SalesType = $MyRow['typeabbrev'];

		$InsuranceTypeID = $_POST['InsuranceType'];

		if (!isset($_POST['New'])) {

			$SQL = "SELECT count(id)
					  FROM debtortrans
					where debtorno = '" . $_POST['DebtorNo'] . "'";
			$Result = DB_query($SQL);
			$MyRow = DB_fetch_array($Result);

			if ($MyRow[0] == 0) {
				$SQL = "UPDATE debtorsmaster SET
					name='" . $_POST['CustName'] . "',
					address1='" . $_POST['Address1'] . "',
					address2='" . $_POST['Address2'] . "',
					address3='" . $_POST['Address3'] . "',
					address4='" . $_POST['Address4'] . "',
					address5='" . $_POST['Address5'] . "',
					address6='" . $_POST['Address6'] . "',
					currcode='" . $_POST['CurrCode'] . "',
					paymentterms='" . $_POST['PaymentTerms'] . "',
					taxref='" . $_POST['TaxRef'] . "',
					typeid='" . $InsuranceTypeID . "'
				  WHERE debtorno = '" . $_POST['DebtorNo'] . "'";
			} else {

				$currsql = "SELECT currcode
					  		FROM debtorsmaster
							where debtorno = '" . $_POST['DebtorNo'] . "'";
				$currresult = DB_query($currsql);
				$currrow = DB_fetch_array($currresult);
				$OldCurrency = $currrow[0];

				$SQL = "UPDATE debtorsmaster SET
					name='" . $_POST['CustName'] . "',
					address1='" . $_POST['Address1'] . "',
					address2='" . $_POST['Address2'] . "',
					address3='" . $_POST['Address3'] . "',
					address4='" . $_POST['Address4'] . "',
					address5='" . $_POST['Address5'] . "',
					address6='" . $_POST['Address6'] . "',
					taxref='" . $_POST['TaxRef'] . "',
					typeid='" . $InsuranceTypeID . "'
				  WHERE debtorno = '" . $_POST['DebtorNo'] . "'";

				if ($OldCurrency != $_POST['CurrCode']) {
					prnMsg(_('The currency code cannot be updated as there are already transactions for this customer'), 'info');
				}
			}

			$ErrMsg = _('The customer could not be updated because');
			$Result = DB_query($SQL, $ErrMsg);
			prnMsg(_('Customer updated'), 'success');
			echo '<br />';
			if (isset($_SESSION['Care2xDatabase']) and $_SESSION['Care2xDatabase'] != 'None') {
				$SQL = "UPDATE " . $_SESSION['Care2xDatabase'] . ".care_insurance_firm SET name='" . $_POST['CustName'] . "',
																							iso_country_id='" . $_POST['Address6'] . "',
																							type_nr='" . $InsuranceTypeID . "',
																							addr='" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							addr_mail='" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							addr_billing='" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							modify_id='KwaMoja'
																						WHERE firm_id='" . $_POST['DebtorNo'] . "'";
				$ErrMsg = _('This company could not be updated to the care2x database because');
				$Result = DB_query($SQL, $ErrMsg);
			}
		} else { //it is a new customer
			/* set the DebtorNo if $AutoDebtorNo in config.php has been set to
			something greater 0 */

			$SQL = "INSERT INTO debtorsmaster (
							debtorno,
							name,
							address1,
							address2,
							address3,
							address4,
							address5,
							address6,
							currcode,
							holdreason,
							salestype,
							paymentterms,
							taxref,
							typeid)
				VALUES ('" . $_POST['DebtorNo'] . "',
					'" . $_POST['CustName'] . "',
					'" . $_POST['Address1'] . "',
					'" . $_POST['Address2'] . "',
					'" . $_POST['Address3'] . "',
					'" . $_POST['Address4'] . "',
					'" . $_POST['Address5'] . "',
					'" . $_POST['Address6'] . "',
					'" . $_POST['CurrCode'] . "',
					'1',
					'" . $SalesType . "',
					'" . $_POST['PaymentTerms'] . "',
					'" . $_POST['TaxRef'] . "',
					'" . $InsuranceTypeID . "'
					)";

			$ErrMsg = _('This company could not be added because');
			$Result = DB_query($SQL, $ErrMsg);

			$BranchCode = $_POST['DebtorNo'];

			$SQL = "INSERT INTO custbranch (
							branchcode,
							debtorno,
							brname,
							braddress1,
							braddress2,
							braddress3,
							braddress4,
							braddress5,
							braddress6,
							area,
							salesman,
							taxgroupid,
							defaultlocation,
							defaultshipvia)
						VALUES (
							'" . $BranchCode . "',
							'" . $_POST['DebtorNo'] . "',
							'" . $_POST['CustName'] . "',
							'" . $_POST['Address1'] . "',
							'" . $_POST['Address2'] . "',
							'" . $_POST['Address3'] . "',
							'" . $_POST['Address4'] . "',
							'" . $_POST['Address5'] . "',
							'" . $_POST['Address6'] . "',
							'" . $_POST['Area'] . "',
							'" . $_POST['Salesman'] . "',
							'" . $_POST['TaxGroup'] . "',
							'" . $_SESSION['UserStockLocation'] . "',
							'1'
						)";
			$ErrMsg = _('This company could not be added because');
			$Result = DB_query($SQL, $ErrMsg);

			if (isset($_SESSION['Care2xDatabase']) and $_SESSION['Care2xDatabase'] != 'None') {
				$SQL = "INSERT INTO " . $_SESSION['Care2xDatabase'] . ".care_insurance_firm (firm_id,
																							name,
																							iso_country_id,
																							type_nr,
																							addr,
																							addr_mail,
																							addr_billing,
																							create_id
																						) VALUES (
																							'" . $_POST['DebtorNo'] . "',
																							'" . $_POST['CustName'] . "',
																							'" . $_POST['Address6'] . "',
																							'" . $InsuranceTypeID . "',
																							'" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							'" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							'" . $_POST['Address1'] . ', ' . $_POST['Address2'] . ', ' . $_POST['Address3'] . ', ' . $_POST['Address4'] . ', ' . $_POST['Address5'] . "',
																							'KwaMoja'
																						)";
				$ErrMsg = _('This company could not be added to the care2x database because');
				$Result = DB_query($SQL, $ErrMsg);
			}

			prnMsg(_('The Insurance Company has been successfully created'), 'success');

			include('includes/footer.inc');
			exit;
		}
	} else {
		prnMsg(_('Validation failed') . '. ' . _('No updates or deletes took place'), 'error');
	}
	unset($_POST['DebtorNo']);
	unset($_POST['CustName']);
	unset($_POST['Address1']);
	unset($_POST['Address2']);
	unset($_POST['Address3']);
	unset($_POST['Address4']);
	unset($_POST['Address5']);
	unset($_POST['Address6']);
} elseif (isset($_POST['delete'])) {

	//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'

	$SQL = "SELECT COUNT(*) FROM debtortrans WHERE debtorno='" . $_POST['DebtorNo'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0) {
		$CancelDelete = 1;
		prnMsg(_('This company cannot be deleted because there are transactions that refer to it'), 'warn');
		echo '<br /> ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('transactions against this company');

	} else {
		$SQL = "SELECT COUNT(*) FROM salesorders WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			$CancelDelete = 1;
			prnMsg(_('Cannot delete the company record because orders have been created against it'), 'warn');
			echo '<br /> ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('orders against this company');
		} else {
			$SQL = "SELECT COUNT(*) FROM salesanalysis WHERE cust='" . $_POST['DebtorNo'] . "'";
			$Result = DB_query($SQL);
			$MyRow = DB_fetch_row($Result);
			if ($MyRow[0] > 0) {
				$CancelDelete = 1;
				prnMsg(_('Cannot delete this company record because sales analysis records exist for it'), 'warn');
				echo '<br /> ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('sales analysis records against this company');
			} else {
				$SQL = "SELECT COUNT(*) FROM custbranch WHERE debtorno='" . $_POST['DebtorNo'] . "'";
				$Result = DB_query($SQL);
				$MyRow = DB_fetch_row($Result);
				if ($MyRow[0] > 0) {
					$CancelDelete = 1;
					prnMsg(_('Cannot delete this company because there are branch records set up against it'), 'warn');
					echo '<br /> ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('branch records relating to this company');
				}
			}
		}

	}
	if ($CancelDelete == 0) { //ie not cancelled the delete as a result of above tests
		$SQL = "DELETE FROM custcontacts WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$Result = DB_query($SQL);
		$SQL = "DELETE FROM debtorsmaster WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$Result = DB_query($SQL);
		prnMsg(_('company') . ' ' . $_POST['DebtorNo'] . ' ' . _('has been deleted - together with all the associated contacts') . ' !', 'success');
		include('includes/footer.inc');
		unset($_SESSION['CustomerID']);
		exit;
	} //end if Delete Customer
}

if (isset($reset)) {
	unset($_POST['CustName']);
	unset($_POST['Address1']);
	unset($_POST['Address2']);
	unset($_POST['Address3']);
	unset($_POST['Address4']);
	unset($_POST['Address5']);
	unset($_POST['Address6']);
	unset($_POST['Phone']);
	unset($_POST['Fax']);
	unset($_POST['Email']);
	unset($_POST['HoldReason']);
	unset($_POST['PaymentTerms']);
	unset($_POST['Discount']);
	unset($_POST['DiscountCode']);
	unset($_POST['PymtDiscount']);
	unset($_POST['CreditLimit']);
	// Leave Sales Type set so as to faciltate fast customer setup
	//	unset($_POST['SalesType']);
	unset($_POST['DebtorNo']);
	unset($_POST['InvAddrBranch']);
	unset($_POST['TaxRef']);
	unset($_POST['CustomerPOLine']);
	// Leave Type ID set so as to faciltate fast customer setup
	//	unset($_POST['typeid']);
}

/*DebtorNo could be set from a post or a get when passed as a parameter to this page */

if (isset($_POST['DebtorNo'])) {
	$DebtorNo = $_POST['DebtorNo'];
} elseif (isset($_GET['DebtorNo'])) {
	$DebtorNo = $_GET['DebtorNo'];
}
if (isset($_POST['ID'])) {
	$ID = $_POST['ID'];
} elseif (isset($_GET['ID'])) {
	$ID = $_GET['ID'];
} else {
	$ID = '';
}
if (isset($_POST['ws'])) {
	$ws = $_POST['ws'];
} elseif (isset($_GET['ws'])) {
	$ws = $_GET['ws'];
}
if (isset($_POST['Edit'])) {
	$Edit = $_POST['Edit'];
} elseif (isset($_GET['Edit'])) {
	$Edit = $_GET['Edit'];
} else {
	$Edit = '';
}

if (isset($_POST['Add'])) {
	$Add = $_POST['Add'];
} elseif (isset($_GET['Add'])) {
	$Add = $_GET['Add'];
}

/*If the page was called without $_POST['DebtorNo'] passed to page then assume a new customer is to be entered show a form with a Debtor Code field other wise the form showing the fields with the existing entries against the customer will show for editing with only a hidden DebtorNo field*/

/* First check that all the necessary items have been setup */

$SetupErrors = 0; //Count errors
$SQL = "SELECT COUNT(typeabbrev)
				FROM salestypes";
$Result = DB_query($SQL);
$MyRow = DB_fetch_row($Result);
if ($MyRow[0] == 0) {
	prnMsg(_('In order to create a new customer you must first set up at least one sales type/price list') . '<br />' . _('Click') . ' ' . '<a target="_blank" href="' . $RootPath . '/SalesTypes.php">' . _('here') . ' ' . '</a>' . _('to set up your price lists'), 'warning') . '<br />';
	$SetupErrors += 1;
}
$SQL = "SELECT COUNT(typeid)
			FROM debtortype";
$Result = DB_query($SQL);
$MyRow = DB_fetch_row($Result);
if ($MyRow[0] == 0) {
	prnMsg(_('In order to create a new customer you must first set up at least one customer type') . '<br />' . _('Click') . ' ' . '<a target="_blank" href="' . $RootPath . '/CustomerTypes.php">' . _('here') . ' ' . '</a>' . _('to set up your customer types'), 'warning');
	$SetupErrors += 1;
}

if ($SetupErrors > 0) {
	echo '<br /><div class=centre><a href="' . $_SERVER['PHP_SELF'] . '" >' . _('Click here to continue') . '</a></div>';
	include('includes/footer.inc');
	exit;
}

echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<table class="selection">';

if (!isset($DebtorNo)) {
	echo '<tr>
			<td>' . _('Company Code') . ':</td>
			<td><input tabindex="1" type="text" name="DebtorNo" size="11" maxlength="10"></td>
		</tr>';
	$_POST['CustName'] = '';
	$_POST['Phone'] = '';
	$_POST['Facsimile'] = '';
	$_POST['Address1'] = '';
	$_POST['Address2'] = '';
	$_POST['Address3'] = '';
	$_POST['Address4'] = '';
	$_POST['Address5'] = '';
	$_POST['Address6'] = '';
	$_POST['Email'] = '';
	$_POST['TaxRef'] = '';
} else {
	echo '<input type="hidden" name="DebtorNo" value="' . $DebtorNo . '" />';
	echo '<tr>
			<td>' . _('Company Code') . ':</td>
			<td>' . $DebtorNo . '</td>
		</tr>';
	$SQL = "SELECT debtorsmaster.debtorno,
					name,
					address1,
					address2,
					address3,
					address4,
					address5,
					address6,
					currencies.currency,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.email,
					taxref,
					typeid
				FROM debtorsmaster
				LEFT JOIN custbranch
					ON debtorsmaster.debtorno=custbranch.debtorno
				LEFT JOIN currencies
					ON debtorsmaster.currcode=currencies.currabrev
				WHERE debtorsmaster.debtorno='" . $DebtorNo . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);

	$_POST['CustName'] = $MyRow['name'];
	$_POST['Phone'] = $MyRow['phoneno'];
	$_POST['Facsimile'] = $MyRow['faxno'];
	$_POST['Address1'] = $MyRow['address1'];
	$_POST['Address2'] = $MyRow['address2'];
	$_POST['Address3'] = $MyRow['address3'];
	$_POST['Address4'] = $MyRow['address4'];
	$_POST['Address5'] = $MyRow['address5'];
	$_POST['Address6'] = $MyRow['address6'];
	$_POST['Email'] = $MyRow['email'];
	$_POST['TaxRef'] = $MyRow['taxref'];
}

echo '<tr>
		<td>' . _('Company Name') . ':</td>
		<td><input tabindex=2 type="text" name="CustName" size="42" maxlength="40" value="' . $_POST['CustName'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Telephone') . ':</td>
		<td><input tabindex=2 type="text" name="Phone" size="30" maxlength="40" value="' . $_POST['Phone'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Facsimile') . ':</td>
		<td><input tabindex=2 type="text" name="Fax" size="30" maxlength="40" value="' . $_POST['Facsimile'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Email Address') . ':</td>
		<td><input tabindex=2 type="text" name="Email" size="30" maxlength="40" value="' . $_POST['Email'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Address Line 1') . ':</td>
		<td><input tabindex=3 type="text" name="Address1" size="42" maxlength="40" value="' . $_POST['Address1'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Address Line 2') . ':</td>
		<td><input tabindex=4 type="text" name="Address2" size="42" maxlength="40" value="' . $_POST['Address2'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Address Line 3') . ':</td>
		<td><input tabindex=5 type="text" name="Address3" size="42" maxlength="40" value="' . $_POST['Address3'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Address Line 4') . ':</td>
		<td><input tabindex=6 type="text" name="Address4" size="42" maxlength="40" value="' . $_POST['Address4'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Address Line 5') . ':</td>
		<td><input tabindex=7 type="text" name="Address5" size="22" maxlength="20" value="' . $_POST['Address5'] . '"></td>
	</tr>';
echo '<tr>
		<td>' . _('Country') . ':</td>
		<td><select minlength="0" name="Address6">';
foreach ($CountriesArray as $CountryEntry => $CountryName) {
	if (isset($_POST['Address6']) and (strtoupper($_POST['Address6']) == strtoupper($CountryName))) {
		echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
	} elseif (!isset($_POST['Address6']) and $CountryName == "") {
		echo '<option selected="selected" value="' . $CountryName . '">' . $CountryName . '</option>';
	} else {
		echo '<option value="' . $CountryName . '">' . $CountryName . '</option>';
	}
} //$CountriesArray as $CountryEntry => $CountryName
echo '</select>
			</td>
		</tr>';

echo '<tr>
		<td>' . _('Tax Reference') . ':</td>
		<td><input tabindex=15 type="text" name="TaxRef" size="22" maxlength="20" value="' . $_POST['TaxRef'] . '"></td>
	</tr>';
// Show Sales Type drop down list
$TypeResult = DB_query("SELECT typeid, typename FROM debtortype WHERE typename like '%insurance%'");
if (DB_num_rows($TypeResult) == 0) {
	$DataError = 1;
	echo '<tr>
			<td>' . _('Insurance Type') . ':</td>
			<td>' . _('No insurance company types defined') . '</td><td><a class="ButtonLink" href="InsuranceCompanyTypes.php?" target="_parent">Setup Types</a></td>
		</tr>';
	echo '';
} else {
	echo '<tr>
			<td>' . _('Insurance Type') . ':</td>
			<td><select tabindex="9" name="InsuranceType">';
	echo '<option value=""></option>';
	while ($MyTypeRow = DB_fetch_array($TypeResult)) {
		if (isset($MyRow['typeid']) and $MyRow['typeid'] == $MyTypeRow['typeid']) {
			echo '<option selected="selected" value="' . $MyTypeRow['typeid'] . '">' . $MyTypeRow['typename'] . '</option>';
		} else {
			echo '<option value="' . $MyTypeRow['typeid'] . '">' . $MyTypeRow['typename'] . '</option>';
		}
	} //end while loopre
	echo '</select>
				</td>
			</tr>';
}

$Result = DB_query("SELECT terms, termsindicator FROM paymentterms");
if (DB_num_rows($Result) == 0) {
	$DataError = 1;
	echo '<tr>
			<td>' . _('Payment Terms') . ':</td>
			<td>' . _('There are no payment terms currently defined - go to the setup tab of the main menu and set at least one up first') . '</td>
		</tr>';
} else {

	echo '<tr>
			<td>' . _('Payment Terms') . ':</td>
			<td><select tabindex=15 name="PaymentTerms">';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<option value="' . $MyRow['termsindicator'] . '">' . $MyRow['terms'] . '</option>';
	} //end while loop
	DB_data_seek($Result, 0);

	echo '</select>
				</td>
			</tr>';
}
$Result = DB_query("SELECT currency, currabrev FROM currencies");
if (DB_num_rows($Result) == 0) {
	$DataError = 1;
	echo '<tr>
			<td>' . _('Customer Currency') . ':</td>
			<td>' . _('There are no currencies currently defined - go to the setup tab of the main menu and set at least one up first') . '</td>
		</tr>';
} else {
	if (!isset($_POST['CurrCode'])) {
		$CurrResult = DB_query("SELECT currencydefault FROM companies WHERE coycode=1");
		$MyRow = DB_fetch_row($CurrResult);
		$_POST['CurrCode'] = $MyRow[0];
	}
	echo '<tr>
			<td>' . _('Customer Currency') . ':</td>
			<td><select tabindex=17 name="CurrCode">';
	while ($MyRow = DB_fetch_array($Result)) {
		if ($_POST['CurrCode'] == $MyRow['currabrev']) {
			echo '<option selected value=' . $MyRow['currabrev'] . '>' . $MyRow['currency'] . '</option>';
		} else {
			echo '<option value=' . $MyRow['currabrev'] . '>' . $MyRow['currency'] . '</option>';
		}
	} //end while loop
	DB_data_seek($Result, 0);

	echo '</select>
				</td>
			</tr>';
}

$SQL = "SELECT areacode, areadescription FROM areas";
$Result = DB_query($SQL);
if (DB_num_rows($Result) == 0) {
	$DataError = 1;
	echo '<tr>
			<td>' . _('Sales Area') . ':</td>
			<td>' . _('There are no areas defined as yet') . ' - ' . _('customer branches must be allocated to an area') . '</td>
		</tr>';
} else {
	echo '<tr>
			<td>' . _('Sales Area') . ':</td>
			<td><select tabindex=14 name="Area">';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($_POST['Area']) and $MyRow['areacode'] == $_POST['Area']) {
			echo '<option selected value="' . $MyRow['areacode'] . '">' . $MyRow['areadescription'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['areacode'] . '">' . $MyRow['areadescription'] . '</option>';
		}
	} //end while loop
	echo '</select>
				</td>
			</tr>';
}

$SQL = "SELECT salesmanname, salesmancode FROM salesman";
$Result = DB_query($SQL);
echo '<tr>
		<td>' . _('Salesperson') . ':</td>
		<td><select tabindex=13 name="Salesman">';

while ($MyRow = DB_fetch_array($Result)) {
	if (isset($_POST['Salesman']) and $MyRow['salesmancode'] == $_POST['Salesman']) {
		echo '<option selected value="' . $MyRow['salesmancode'] . '">' . $MyRow['salesmanname'] . '</option>';
	} else {
		echo '<option value="' . $MyRow['salesmancode'] . '">' . $MyRow['salesmanname'] . '</option>';
	}
} //end while loop
echo '</select>
			</td>
		</tr>';

echo '<tr>
		<td>' . _('Tax Group') . ':</td>
		<td><select tabindex=19 name="TaxGroup">';

$SQL = "SELECT taxgroupid, taxgroupdescription FROM taxgroups";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	if (isset($_POST['TaxGroup']) and $MyRow['taxgroupid'] == $_POST['TaxGroup']) {
		echo '<option selected value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
	} else {
		echo '<option value="' . $MyRow['taxgroupid'] . '">' . $MyRow['taxgroupdescription'] . '</option>';
	}

} //end while loop

echo '</select>
			</td>
		</tr>';

echo '</table>';

if (isset($DebtorNo)) {
	$SQL = "SELECT * FROM custcontacts where debtorno='" . $DebtorNo . "' ORDER BY contid";
	$Result = DB_query($SQL);

	echo '<table class=selection>';
	if (isset($_GET['Modify'])) {
		echo '<tr>
				<th>' . _('Name') . '</th>
				<th>' . _('Role') . '</th>
				<th>' . _('Phone Number') . '</th>
				<th>' . _('Notes') . '</th>
			</tr>';
	} else {
		echo '<tr>
				<th>' . _('Name') . '</th>
				<th>' . _('Role') . '</th>
				<th>' . _('Phone Number') . '</th>
				<th>' . _('Notes') . '</th>
				<th>' . _('Edit') . '</th>
				<th colspan=2><button type="submit" name="addcontact">' . _('Add Contact') . '</button></th>
			</tr>';
	}
	$k = 0; //row colour counter

	while ($MyRow = DB_fetch_array($Result)) {
		if ($k == 1) {
			echo '<tr class="OddTableRows">';
			$k = 0;
		} else {
			echo '<tr class="EvenTableRows">';
			$k = 1;
		}

		if (isset($_GET['Modify'])) {
			printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				</tr>', $MyRow[2], $MyRow[3], $MyRow[4], $MyRow[5], $MyRow[0], $MyRow[1], $MyRow[1]);
		} else {
			printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="AddCustomerContacts.php?Id=%s&DebtorNo=%s">' . _('Edit') . '</a></td>
				<td><a href="%sID=%s&DebtorNo=%s&delete=1">' . _('Delete') . '</a></td>
				</tr>', $MyRow[2], $MyRow[3], $MyRow[4], $MyRow[5], $MyRow[0], $MyRow[1], $_SERVER['PHP_SELF'] . '?', $MyRow[0], $MyRow[1]);
		}
	} //END WHILE LIST LOOP
	echo '</table>';
	//	echo "<input type='Submit' name='addcontact' value='" . _('ADD Contact') . "'>";
	echo '<form method="post" action=' . $_SERVER['PHP_SELF'] . '?DebtorNo="' . $DebtorNo . '"&ID=' . $ID . '&Edit' . $Edit . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if (isset($Edit) and $Edit != '') {
		$SQLcustcontacts = "SELECT * from custcontacts
							WHERE debtorno='" . $DebtorNo . "'
							and contid='" . $ID . "'";
		$Resultcc = DB_query($SQLcustcontacts);
		$MyRowcc = DB_fetch_array($Resultcc);
		$_POST['custname'] = $MyRowcc['contactname'];
		$_POST['role'] = $MyRowcc['role'];
		$_POST['phoneno'] = $MyRowcc['phoneno'];
		$_POST['notes'] = $MyRowcc['notes'];
		echo '<table class=selection>';
		echo '<tr>
				<td>' . _('Name') . '</td><td><input type=text name="custname" value="' . $_POST['custname'] . '"></td></tr><tr>
				<td>' . _('Role') . '</td><td><input type=text name="role" value="' . $_POST['role'] . '"></td></tr><tr>
				<td>' . _('Phone no') . '</td><td><input type="text" name="phoneno" value="' . $_POST['phoneno'] . '"></td></tr><tr>
				<td>' . _('Notes') . '</td><td><textarea name="notes">' . $_POST['notes'] . '</textarea></td></tr>
				<tr><td colspan=2><div class="centre"><button type="submit" name="update">' . _('Update') . '</td></tr></table>';

	}
	if (isset($_POST['update'])) {

		$SQLupdatecc = "UPDATE custcontacts
						SET contactname='" . $_POST['custname'] . "',
						role='" . $_POST['role'] . "',
						phoneno='" . $_POST['phoneno'] . "',
						notes='" . DB_escape_string($_POST['notes']) . "'
						Where debtorno='" . $DebtorNo . "'
						and contid='" . $Edit . "'";
		$Resultupcc = DB_query($SQLupdatecc);
		echo '<br />' . $SQLupdatecc;
		echo '<meta http-equiv="Refresh" content="0; url="' . $_SERVER['PHP_SELF'] . '?DebtorNo=' . $DebtorNo . '&ID=' . $ID . '">';
	}
	if (isset($_GET['delete'])) {
		$SQl = "DELETE FROM custcontacts where debtorno='" . $DebtorNo . "'
				and contid='" . $ID . "'";
		$Resultupcc = DB_query($SQl);

		echo '<meta http-equiv="Refresh" content="0; url=' . $_SERVER['PHP_SELF'] . '?DebtorNo=' . $DebtorNo . '">';
		echo '<br />' . $SQl;
		prnmsg('Contact Deleted', 'success');
	}


	echo '</td></tr></table>';
	// end of main ifs
}
if (!isset($DebtorNo)) {
	echo '<input type="submit" name="submit" value="' . _('Add New Company') . '" />
			<input type="submit" name="reset" value="' . _('Reset') . '" />';
	echo '<input type="hidden" name="New" value="True" />';
} else {
	echo '<input type="submit" name="submit" value="' . _('Update Company') . '" />';
	echo '<input type="submit" name="delete" value="' . _('Delete Company') . '" />';
}
if (isset($_POST['addcontact']) and (isset($_POST['addcontact']) != '')) {
	echo '<meta http-equiv="Refresh" content="0; url=' . $RootPath . '/AddCustomerContacts.php?DebtorNo=' . $DebtorNo . '">';
}
echo '</form>';

if (!isset($_GET['Edit'])) {
	$SQL = "SELECT debtorno,
				name,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				currencies.currency
			FROM debtorsmaster
			LEFT JOIN debtortype
				ON debtorsmaster.typeid=debtortype.typeid
			LEFT JOIN currencies
				ON debtorsmaster.currcode=currencies.currabrev
			WHERE debtortype.typename like '%Insurance%'";
	$Result = DB_query($SQL);

	echo '<table class="selection">
			<tr>
				<th>' . _('Company No') . '</th>
				<th>' . _('Name') . '</th>
				<th>' . _('Address1') . '</th>
				<th>' . _('Address2') . '</th>
				<th>' . _('Address3') . '</th>
				<th>' . _('Address4') . '</th>
				<th>' . _('Address5') . '</th>
				<th>' . _('Address6') . '</th>
				<th>' . _('Currency') . '</th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr>
				<td>' . $MyRow['debtorno'] . '</td>
				<td>' . $MyRow['name'] . '</td>
				<td>' . $MyRow['address1'] . '</td>
				<td>' . $MyRow['address2'] . '</td>
				<td>' . $MyRow['address3'] . '</td>
				<td>' . $MyRow['address4'] . '</td>
				<td>' . $MyRow['address5'] . '</td>
				<td>' . $MyRow['address6'] . '</td>
				<td>' . $MyRow['currency'] . '</td>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?Debtor=' . urlencode($MyRow['debtorno']) . '&Edit=True">' . _('Edit') . '</a></td>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?Debtor=' . urlencode($MyRow['debtorno']) . '&Delete=True">' . _('Delete') . '</a></td>
			</tr>';
	}
	echo '</table>';
}

include('includes/footer.inc');
?>