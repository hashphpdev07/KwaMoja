function CreateMask() {
	divMask = document.createElement("div");
	divMask.className = "mask";
	document.body.appendChild(divMask);
};

function CreateModal(id, section, caption) {
	divModal = document.createElement("div");
	divModal.className = "modal_window";
	document.body.appendChild(divModal);
	modalHeader = document.createElement("div");
	modalHeader.className = "modal_header";
	modalHeader.innerHTML=caption;
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
	modalHeaderHelp = document.createElement("div");
	modalHeaderHelp.className = "modal_exit";
	modalHeaderHelp.innerHTML ="?";
	modalHeaderHelp.onclick = Help;
	modalHeader.appendChild(modalHeaderHelp);
	modalFooter = document.createElement("div");
	modalFooter.className = "modal_footer";
	modalFooter.innerHTML="KwaMoja version "+version;
	modalFooterClock = document.createElement("div");
	modalFooterClock.className="modal_clock";
	refreshClock();
	modalFooter.appendChild(modalFooterClock);
	divModal.appendChild(modalFooter);
	ExpandWindow(51, 51, 51, 51, 1, 1, 1, 1);
};

function refreshClock() {
	var mydate=new Date();
	var year=mydate.getYear();
	if (year < 1000)
		year+=1900;
	var day=mydate.getDay();
	var month=mydate.getMonth();
	var daym=mydate.getDate();
	if (daym<10)
		daym="0"+daym;
	modalFooterClock.innerHTML=longdays[day]+", "+longmonths[month]+" "+daym+", "+year;
}

function Redirect(e) {
	GetContent(1, e.getAttribute("href").replace(/^.*[\\\/]/, ''));
}

function OverRideClicks() {
	if (document.getElementsByTagName) {
		var e = document.getElementsByTagName("a");
		for (i = 0; i < e.length; i++) {
			var t = e[i];
			if (t.getAttribute("href")!="#" && t.id!="exit") {
				t.target = "_blank"
				t.onclick = function () {Redirect(this); return false};
			}
		}
		var e = document.getElementsByTagName("input");
		for (i = 0; i < e.length; i++) {
			var t = e[i];
			if (t.getAttribute("type")=='submit') {
				t.onclick = function () {SubmitThisForm(t.form,modalContents); return false};
			}
		}
	}
	var n = divModal.getElementsByTagName("table");
	for (i = 0; i < n.length; i++) {
		if (n[i].parentNode.tagName == "FORM") {
			n[i].className = "input_form";
		}
	}
	ShowMessages();
}

function GetContent(id, section) {
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
//			data=JSON.parse(xmlhttp.responseText);
//			modalHeader.innerHTML=document.getElementById('title').innerHTML;
			modalContents.innerHTML=xmlhttp.responseText;
			OverRideClicks();
		}
	}
	xmlhttp.open("GET",section,false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Pragma","no-cache");
	xmlhttp.send();
	return false;
};

function Show(id, section='', caption='') {
	CreateMask();
	CreateModal(id, section, caption);
};

function Remove() {
	CollapseWindow(1, 1, 1, 1, 51, 51, 51, 51);
};

function Help() {
}

function ExpandWindow(StartL, StartR, StartT, StartB, EndL, EndR, EndT, EndB) {
	function Expand() {
		StartL--;
		StartL--;
		StartR--;
		StartR--;
		StartT--;
		StartT--;
		StartB--;
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
			modalFooter.style.display = 'block';
		}
	}
	Expand();
};

function CollapseWindow(StartL, StartR, StartT, StartB, EndL, EndR, EndT, EndB) {
	modalHeader.style.display = 'none';
	modalContents.style.display = 'none';
	modalFooter.style.display = 'none';
	function Collapse() {
		StartL++;
		StartL++;
		StartR++;
		StartR++;
		StartT++;
		StartT++;
		StartB++;
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

function SubmitThisForm(FormName, Element) {
	Target=FormName.action;
	var PostData='';
	for(var i=0,fLen=FormName.length;i<fLen;i++){
		if(FormName.elements[i].type=='checkbox' && !FormName.elements[i].checked) {
			FormName.elements[i].value=null;
		}
		PostData=PostData+FormName.elements[i].name+'='+FormName.elements[i].value+'&';
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			Element.innerHTML=xmlhttp.responseText;
			OverRideClicks();
		}
	}
	xmlhttp.open("POST",Target,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Pragma","no-cache");
	xmlhttp.send(PostData);
	return false;
}