<?php

AddColumn('department', 'tags', 'INT(11)', 'NOT NULL', '0', 'tagref');

UpdateDBNo(basename(__FILE__, '.php'));

?>