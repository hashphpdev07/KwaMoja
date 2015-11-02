function CreateMask() {
	divMask = document.createElement("div");
	divMask.className = "mask";
	document.body.appendChild(divMask);
};

function CreateModal(id, section) {
	divModal = document.createElement("div");
	divModal.className = "modal_window";
	document.body.appendChild(divModal);
	modalHeader = document.createElement("div");
	modalHeader.className = "modal_header";
	divModal.appendChild(modalHeader);
	modalContents = document.createElement("div");
	modalContents.className = "modal_contents";
	divModal.appendChild(modalContents);
	GetContent(id, section);
	modalHeaderExit = document.createElement("div");
	modalHeaderExit.className = "modal_exit";
	modalHeaderExit.innerHTML ="X";
	modalHeaderExit.onclick = Remove;
	modalHeader.appendChild(modalHeaderExit);
	ExpandWindow(51, 51, 51, 51, 1, 1, 1, 1);
};

function GetContent(id, section) {
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			data=JSON.parse(xmlhttp.responseText);
			modalHeader.innerHTML=data['title'];
			modalContents.innerHTML=data['content'];
		}
	}
	xmlhttp.open("GET",'Pages/Page'+id+'.php?Section='+section,false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Pragma","no-cache");
	xmlhttp.send();
	return false;
};

function Show(id, section='') {
	CreateMask();
	CreateModal(id, section);
};

function Remove() {
	CollapseWindow(1, 1, 1, 1, 51, 51, 51, 51);
};

function ExpandWindow(StartL, StartR, StartT, StartB, EndL, EndR, EndT, EndB) {
	function Expand() {
		StartL--;
		StartR--;
		StartT--;
		StartB--;
		divModal.style.left = StartL + '%'; // pseudo-property code: Move right by 10px
		divModal.style.right = StartR + '%'; // pseudo-property code: Move right by 10px
		divModal.style.top = StartT + '%'; // pseudo-property code: Move right by 10px
		divModal.style.bottom = StartB + '%'; // pseudo-property code: Move right by 10px
		if ((StartL > EndL) && (StartR > EndR) && (StartT > EndT) && (StartB > EndB)) {
			setTimeout(Expand, 1); // call doMove() in 20 msec
		} else {
			modalHeader.style.display = 'block';
			modalContents.style.display = 'block';
		}
	}
	Expand();
};

function CollapseWindow(StartL, StartR, StartT, StartB, EndL, EndR, EndT, EndB) {
	modalHeader.style.display = 'none';
	modalContents.style.display = 'none';
	function Collapse() {
		StartL++;
		StartR++;
		StartT++;
		StartB++;
		divModal.style.left = StartL + '%'; // pseudo-property code: Move right by 10px
		divModal.style.right = StartR + '%'; // pseudo-property code: Move right by 10px
		divModal.style.top = StartT + '%'; // pseudo-property code: Move right by 10px
		divModal.style.bottom = StartB + '%'; // pseudo-property code: Move right by 10px
		if ((StartL < EndL) && (StartR < EndR) && (StartT < EndT) && (StartB < EndB)) {
			setTimeout(Collapse, 1); // call doMove() in 20 msec
		} else {
			document.body.removeChild(divModal);
			document.body.removeChild(divMask);
		}
	}
	Collapse();
};