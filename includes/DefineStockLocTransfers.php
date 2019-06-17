<?php
/*Class to hold stock transfer records */

class StockLocationTransfer {

	var $TrfID;
	var $FromStockLocation;
	var $FromStockLocationName;
	var $ToStockLocation;
	var $ToStockLocationName;
	var $TranDate;
	var $StockID;
	var $StockQTY;
	var $Container;
	var $LinesCounter;
	/*Array of LineItems */

	function __construct() {
		$this->LinesCounter = 0;
		$this->StockID = array();
		$this->StockQTY = array();
		$this->Container = array();
		/*Array of LineItem s */
	}
}

?>