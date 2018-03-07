<?php

NewScript('Z_RemovePurchaseBackOrders.php', 15);

NewMenuItem('Utilities', 'Transactions', _('Remove all purchase back orders'), '/Z_RemovePurchaseBackOrders.php', 16);

UpdateDBNo(basename(__FILE__, '.php'));

?>