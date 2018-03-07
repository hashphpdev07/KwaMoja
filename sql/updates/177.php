<?php

$SQL = "UPDATE prices SET enddate='9999-12-31' WHERE enddate='0000-00-00'";
$Result = DB_query($SQL);

UpdateDBNo(basename(__FILE__, '.php'));

?>