<?php
/* $Revision: 1.0 $ */

class Tardiness {

	var $TDEntries; /*array of objects class - id is the pointer */
	var $TDDate; /*Date to be processed */
	var $TDItemCounter; /*Counter for the number of entires being posted */
	var $TDItemID;
	var $TDTotal; /*Running total */

	function Tardiness() {
		/*Constructor function initialises */
		$this->TDEntries = array();
		$this->TDItemCounter = 0;
		$this->TDTotal = 0;
		$this->TDTotalAbs = 0;
		$this->TDItemID = 0;
	}
	function Add_TDEntry($TDHours, $TDHoursAbs, $EmployeeID, $LastName, $FirstName, $TDDesc) {
		if ((isset($EmployeeID) and $TDHours != 0) or (isset($EmployeeID) and $TDHoursAbs != 0)) {
			$this->TDEntries[$this->TDItemID] = new TDAnalysis($this->TDItemID, $TDHours, $TDHoursAbs, $EmployeeID, $LastName, $FirstName, $OverTimeDesc);
			$this->TDItemCounter++;
			$this->TDItemID++;
			$this->TDTotal+= $TDHours;
			$this->TDTotalAbs+= $TDHoursAbs;
			return 1;
		}
		return 0;
	}

	function remove_TDEntry($GL_ID) {
		$this->TDTotal-= $this->TDEntries[$GL_ID]->TDHours;
		$this->TDTotalAbs-= $this->TDEntries[$GL_ID]->TDHoursAbs;
		unset($this->TDEntries[$GL_ID]);
		$this->TDItemCounter--;
	}

} /* end of class defintion */
class TDAnalysis {
	var $TDHours;
	var $TDHoursAbs;
	var $EmployeeID;
	var $LastName;
	var $FirstName;
	var $RegTimeDesc;
	var $ID;

	function TDAnalysis($id, $Oth, $OthAbs, $Empcode, $Last, $First, $TDDesc) {

		/* Constructor function to add a new  object with passed params */
		$this->TDHours = $Oth;
		$this->TDHoursAbs = $OthAbs;
		$this->EmployeeID = $Empcode;
		$this->LastName = $Last;
		$this->FirstName = $First;
		$this->OverTimeDesc = $TDDesc;
		$this->ID = $id;
	}
}

?>