<?php

NewScript('BankAccountBalances.php', 15);

NewMenuItem('GL', 'Reports', _('Bank Account Balances'), '/BankAccountBalances.php', 17);

UpdateDBNo(basename(__FILE__, '.php'));

?>