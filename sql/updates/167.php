<?php

AddColumn('cashflowsactivity', 'chartmaster', 'TINYINT(1)', 'NOT NULL', -1, 'group_');

NewConfigValue('PeriodProfitAccount', '1');

NewScript('GLCashFlowsIndirect.php', '8');
NewScript('GLCashFlowsSetup.php', '8');

NewMenuItem('GL', 'Reports', _('Statement of Cash Flows'), '/GLCashFlowsIndirect.php', 10);
NewMenuItem('GL', 'Maintenance', _('Setup GL Accounts for Statement of Cash Flows'), '/GLCashFlowsSetup.php', 16);

UpdateDBNo(basename(__FILE__, '.php'));

?>