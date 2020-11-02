<?php
AddColumn('invoice_nr', 'care_billable_items', 'INT(11)', 'NOT NULL', '0', 'is_billed');

NewScript('KCMCBillingConfiguration.php', 13);
NewMenuItem('hospsetup', 'Reports', _('Billing Configuration'), '/KCMCBillingConfiguration.php', 1);

NewConfigValue('RegistrationBillingItem', '');
NewConfigValue('InpatientAdmissionsBillingItem', '');
NewConfigValue('OutpatientAdmissionsBillingItem', '');

NewConfigValue('LabPaymentBeforeTest', '0');
NewConfigValue('BillForBacteriologyTest', '1');
NewConfigValue('BillForBloodTest', '1');
NewConfigValue('BillForMedicalTest', '1');
NewConfigValue('BillForPathologyTest', '1');

UpdateDBNo(basename(__FILE__, '.php'));

?>