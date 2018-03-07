<?php

NewScript('PcReportExpense.php', '6');

NewMenuItem('PC', 'Reports', _('PC Expense General Report'), '/PcReportExpense.php', 2);

UpdateDBNo(basename(__FILE__, '.php'));

?>