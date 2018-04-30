<?php
ChangeColumnSize('reference', 'debtortrans', 'VARCHAR(50)', 'NOT NULL', "''", 50);

UpdateDBNo(basename(__FILE__, '.php'));

?>