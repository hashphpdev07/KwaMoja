<?php
AddColumn('workcentre', 'worequirements', 'char(5)', 'NOT NULL', '0', 'stockid');
DropPrimaryKey('worequirements', array('wo', 'parentstockid', 'stockid'));
AddPrimaryKey('worequirements', array('wo', 'parentstockid', 'stockid', 'workcentre'));

UpdateDBNo(basename(__FILE__, '.php'));

?>