<?php
NewScript('Z_Fix1cAllocations.php', 15);

NewMenuItem('Utilities', 'Transactions', _('Fully allocate Customer transactions where < 0.01 unallocated'), '/Z_Fix1cAllocations.php', 12);

UpdateDBNo(basename(__FILE__, '.php'));

?>