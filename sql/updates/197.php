<?php
NewScript('prlSelectPayroll.php', 12);
NewMenuItem('HR', 'Transactions', _('Create/Modify/Edit Payroll'), '/prlSelectPayroll.php', 1);
NewScript('prlRegTimeEntry.php', 12);
NewMenuItem('HR', 'Transactions', _('Regular Time Data Entry'), '/prlRegTimeEntry.php', 2);
NewScript('prlOTFile.php', 12);
NewMenuItem('HR', 'Transactions', _('Overtime Data Entry'), '/prlOTFile.php', 3);
NewScript('prlTardiness.php', 12);
NewMenuItem('HR', 'Transactions', _('Lates and Absents  Data Entry'), '/prlTardiness.php', 4);
NewScript('prlOthIncome.php', 12);
NewMenuItem('HR', 'Transactions', _('Other Income Data Entry'), '/prlOthIncome.php', 5);
NewScript('prlSelectLoan.php', 12);
NewMenuItem('HR', 'Transactions', _('View/Edit Employee Loan Data'), '/prlSelectLoan.php', 6);
NewScript('prlSelectRT.php', 12);
NewMenuItem('HR', 'Transactions', _('View Regular Time'), '/prlSelectRT.php', 7);
NewScript('prlSelectOT.php', 12);
NewMenuItem('HR', 'Transactions', _('View Overtime'), '/prlSelectOT.php', 8);
NewScript('prlSelectPayTrans.php', 12);
NewMenuItem('HR', 'Transactions', _('View Payroll Trans'), '/prlSelectPayTrans.php', 9);
NewScript('prlSelectDeduction.php', 12);
NewMenuItem('HR', 'Transactions', _('View Payroll Deduction'), '/prlSelectDeduction.php', 10);
NewScript('prlSelectTD.php', 12);
NewMenuItem('HR', 'Transactions', _('View Lates and Absenses'), '/prlSelectTD.php', 11);
NewScript('prlSelectOthIncome.php', 12);
NewMenuItem('HR', 'Transactions', _('View Other Income Data'), '/prlSelectOthIncome.php', 12);

NewScript('prlRepPayrollRegister.php', 12);
NewMenuItem('HR', 'Reports', _('Payroll Register'), '/prlRepPayrollRegister.php', 1);
NewScript('prlRepBankTrans.php', 12);
NewMenuItem('HR', 'Reports', _('Bank Transmission'), '/prlRepBankTrans.php', 2);
NewScript('prlRepCashTrans.php', 12);
NewMenuItem('HR', 'Reports', _('Over the Counter Listing'), '/prlRepCashTrans.php', 3);
NewScript('prlRepPaySlip.php', 12);
NewMenuItem('HR', 'Reports', _('Pay Slip'), '/prlRepPaySlip.php', 4);
NewScript('prlRepSSSPremium.php', 12);
NewMenuItem('HR', 'Reports', _('SSS Monthly Remittance'), '/prlRepSSSPremium.php', 5);
NewScript('prlRepHDMFPremium.php', 12);
NewMenuItem('HR', 'Reports', _('HDMF MOnthly Remittance'), '/prlRepHDMFPremium.php', 6);
NewScript('prlRepTax.php', 12);
NewMenuItem('HR', 'Reports', _('Tax Monthly Return'), '/prlRepTax.php', 7);
NewScript('prlRepPHPremium.php', 12);
NewMenuItem('HR', 'Reports', _('Philhealth Monthly Remittance'), '/prlRepPHPremium.php', 8);
NewScript('prlRepPayrollRegYTD.php', 12);
NewMenuItem('HR', 'Reports', _('YTD Payroll Register'), '/prlRepPayrollRegYTD.php', 9);
NewScript('prlRepTaxYTD.php', 12);
NewMenuItem('HR', 'Reports', _('Monthly Alphalist of Payees(MAP)'), '/prlRepTaxYTD.php', 10);

NewScript('prlTax.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update Tax Table'), '/prlTax.php', 2);
NewScript('prlSelectTaxStatus.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update Tax Status Table'), '/prlSelectTaxStatus.php', 3);
NewScript('prlSSS.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update SSS Table'), '/prlSSS.php', 4);
NewScript('prlPH.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update PhilHealth Table'), '/prlPH.php', 5);
NewScript('prlHDMF.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update HDMF Table'), '/prlHDMF.php', 6);
NewScript('prlOvertime.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update Overtime Table'), '/prlOvertime.php', 7);
NewScript('prlOthIncTable.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update Other Income Table'), '/prlOthIncTable.php', 8);
NewScript('prlCostCenter.php', 12);
NewMenuItem('HR', 'Maintenance', _('Add/Update Cost Center'), '/prlCostCenter.php', 9);

UpdateDBNo(basename(__FILE__, '.php'));

?>