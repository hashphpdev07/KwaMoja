<?php
AddColumn('container', 'stockmoves', 'VARCHAR(10)', 'NOT NULL', '', 'loccode');
AddIndex(array('stockid', 'loccode', 'container'), 'stockmoves', 'Container');

UpdateDBNo(basename(__FILE__, '.php'));

?>