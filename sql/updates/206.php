<?php
NewScript('Z_ResetStockCosts.php', 15);
NewMenuItem('Utilities', 'Reports', _('Reset Stock Costs table'), '/Z_ResetStockCosts.php', 1);

UpdateDBNo(basename(__FILE__, '.php'));

?>