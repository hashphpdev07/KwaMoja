<?php

ChangeColumnSize('tel', 'purchorders', 'VARCHAR(30)', 'NOT NULL', '', 30);
AddColumn('gender', 'debtorsmaster', 'CHAR(1)', 'NOT NULL', 'm', 'typeid');

NewScript('KCMCAdmission.php', 1);
NewScript('KCMCRegister.php', 1);
NewScript('KCMCEditPatientDetails.php', 1);
NewScript('KCMCFunctionalUnitPL.php', 1);
NewScript('KCMCInsuranceCompanyDetails.php', 1);
NewScript('KCMCLaboratoryReport.php', 1);
NewScript('KCMCInPatientBilling.php', 1);
NewScript('KCMCLabTests.php', 1);
NewScript('KCMCOtherMedicalServices.php', 1);
NewScript('KCMCPharmacy.php', 1);
NewScript('KCMCRadiology.php', 1);
NewScript('KCMCRadiologyReport.php', 1);
NewScript('KCMCUnbilledItems.php', 1);
NewScript('KCMCPatientDeposit.php', 1);
NewScript('KCMCInsuranceInvoice.php', 1);
NewScript('KCMCPrintInsuranceInvoice.php', 1);
NewScript('KCMCSyncCare2xItems.php', 1);
NewScript('PDFPatientReceipt.php', 1);
NewScript('KCMCHospitalConfiguration.php', 15);
NewScript('InsuranceCompanyTypes.php', 15);
NewScript('KCMCAddPatientNotes.php', 15);
NewScript('KCMCViewPatientHistory.php', 15);

NewModule('hospital', 'hsp', _('Hospitals'), 3);

NewMenuItem('hospital', 'Transactions', _('Register Patient'), '/KCMCRegister.php', 1);
NewMenuItem('hospital', 'Transactions', _('Admit Patient'), '/KCMCAdmission.php', 2);
NewMenuItem('hospital', 'Transactions', _('Edit a Patients Details'), '/KCMCEditPatientDetails.php', 3);
NewMenuItem('hospital', 'Transactions', _('In Patient Deposit Payments'), '/KCMCPatientDeposit.php', 4);
NewMenuItem('hospital', 'Transactions', _('Billing For All Drugs and Services'), '/KCMCInPatientBilling.php', 5);
NewMenuItem('hospital', 'Transactions', _('Billing For Pharmaceuticals'), '/KCMCPharmacy.php', 6);
NewMenuItem('hospital', 'Transactions', _('Billing For Laboratory Tests'), '/KCMCLabTests.php', 7);
NewMenuItem('hospital', 'Transactions', _('Billing For Radiology Tests'), '/KCMCRadiology.php', 8);
NewMenuItem('hospital', 'Transactions', _('Billing For Other Medical Services'), '/KCMCOtherMedicalServices.php', 9);
NewMenuItem('hospital', 'Transactions', _('Monthly Insurance Company Billing'), '/KCMCInsuranceInvoice.php', 10);
NewMenuItem('hospital', 'Transactions', _('Add Patient Notes'), '/KCMCAddPatientNotes.php', 10);

NewMenuItem('hospital', 'Reports', _('Income and Expenditure by Functional Unit'), '/KCMCFunctionalUnitPL.php', 1);
NewMenuItem('hospital', 'Reports', _('Financial Report for Laboratory'), '/KCMCLaboratoryReport.php', 2);
NewMenuItem('hospital', 'Reports', _('Financial Report for Radiology Department'), '/KCMCRadiologyReport.php', 3);
NewMenuItem('hospital', 'Reports', _('Items prescribed but not billed'), '/KCMCUnbilledItems.php', 4);
NewMenuItem('hospital', 'Reports', _('Select Invoices/Credit Notes To Print'), '/KCMCPrintInsuranceInvoice.php', 5);
NewMenuItem('hospital', 'Reports', _('View Patient History'), '/KCMCViewPatientHistory.php', 5);

NewMenuItem('hospital', 'Maintenance', _('Create or Modify Insurance Company Details'), '/KCMCInsuranceCompanyDetails.php', 1);
NewMenuItem('hospital', 'Maintenance', _('Synchronise with Care2x Item Table'), '/KCMCSyncCare2xItems.php', 2);

NewMenuItem('system', 'Transactions', _('Configuration Options for Hospital'), '/KCMCHospitalConfiguration.php', 2);
NewMenuItem('system', 'Transactions', _('Create and maintain Insurance Company Types'), '/InsuranceCompanyTypes.php', 2);

NewConfigValue('DispenseOnBill', 1);
NewConfigValue('CanAmendBill', 0);
NewConfigValue('Care2xDatabase', 'None');
NewConfigValue('DefaultArea', '');
NewConfigValue('DefaultSalesPerson', '');

UpdateDBNo(basename(__FILE__, '.php'));

?>