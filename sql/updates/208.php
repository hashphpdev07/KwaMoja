<?php
DropPrimaryKey('salescommissions', array('type', 'transno'));
AddPrimaryKey('salescommissions', array('commissionno', 'type', 'transno'));

UpdateDBNo(basename(__FILE__, '.php'));

?>