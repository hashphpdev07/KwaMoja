<?php
AddColumn('shiploccontainer', 'loctransfers', 'VARCHAR(10)', 'NOT NULL', '', 'shiploc');
AddColumn('recloccontainer', 'loctransfers', 'VARCHAR(10)', 'NOT NULL', '', 'recloc');

DropPrimaryKey('container', array('id'));
AddPrimaryKey('container', array('id', 'location'));

UpdateDBNo(basename(__FILE__, '.php'));

?>