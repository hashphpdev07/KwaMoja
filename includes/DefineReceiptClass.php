<?php

/* definition of the ReceiptBatch class */

class Receipt_Batch {

	var $Items;
	/*array of objects of Receipt class - id is the pointer */
	var $BatchNo;
	/*Batch Number*/
	var $Account;
	/*Bank account GL Code banked into */
	var $AccountCurrency;
	/*Bank Account Currency */
	var $BankAccountName;
	/*Bank account name */
	var $DateBanked;
	/*Date the batch of receipts was banked */
	var $ExRate;
	/*Exchange rate conversion between currency received and bank account currency */
	var $FunctionalExRate;
	/* Exchange Rate between Bank Account Currency and Functional(business reporting) currency */
	var $Currency;
	/*Currency being banked - defaulted to company functional */
	var $CurrDecimalPlaces;
	var $Narrative;
	var $ReceiptType;
	/*Type of receipt ie credit card/cash/cheque etc - array of types defined in config.php*/
	var $total;
	/*Total of the batch of receipts in the currency of the company*/
	var $ItemCounter;
	/*Counter for the number of customer receipts in the batch */

	function __construct() {
		/*Constructor function initialises a new receipt batch */
		$this->Items = array();
		$this->ItemCounter = 0;
		$this->total = 0;
	}

	function add_to_batch($Amount, $Customer, $Discount, $Narrative, $GLCode, $PayeeBankDetail, $CustomerName, $tag) {
		if ((isset($Customer) or isset($GLCode)) and ($Amount + $Discount) != 0) {
			$this->Items[$this->ItemCounter] = new Receipt($Amount, $Customer, $Discount, $Narrative, $this->ItemCounter, $GLCode, $PayeeBankDetail, $CustomerName, $tag);
			$this->ItemCounter++;
			$this->total = $this->total + ($Amount + $Discount) / $this->ExRate;
			return 1;
		}
		return 0;
	}

	function remove_receipt_item($RcptID) {

		$this->total = $this->total - ($this->Items[$RcptID]->Amount + $this->Items[$RcptID]->Discount) / $this->ExRate;
		unset($this->Items[$RcptID]);

	}

}
/* end of class defintion */

class Receipt {
	var $Amount;
	/*in currency of the customer*/
	var $Customer;
	/*customer code */
	var $CustomerName;
	var $Discount;
	var $Narrative;
	var $GLCode;
	var $PayeeBankDetail;
	var $ID;
	var $tag;

	function __construct($Amt, $Cust, $Disc, $Narr, $id, $GLCode, $PayeeBankDetail, $CustomerName, $tag) {

		/* Constructor function to add a new Receipt object with passed params */
		if (count($tag) == 0) {
			$tag = array(0);
		}
		$this->Amount = $Amt;
		$this->Customer = $Cust;
		$this->CustomerName = $CustomerName;
		$this->Discount = $Disc;
		$this->Narrative = $Narr;
		$this->GLCode = $GLCode;
		$this->PayeeBankDetail = $PayeeBankDetail;
		$this->ID = $id;
		$this->tag = $tag;
	}
}

?>