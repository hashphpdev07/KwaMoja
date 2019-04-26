<?php
NewScript('SalesReport.php', 2);
NewMenuItem('orders', 'Reports', _('Sales Report'), '/SalesReport.php', 3);

UpdateDBNo(basename(__FILE__, '.php'));

?>