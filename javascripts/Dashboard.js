var ScriptName='';

function ShowDashboard() {
	var TableRow = new Array();
	var Column = 1;
	var TableCell = new Array();
	TableDashboard = document.createElement("div");
	TableDashboard.className = "dashboard";
	document.body.appendChild(TableDashboard);
	TableRow[Column] = document.createElement("tr");
	TableRow[Column].className = "dashboard_row";
	TableDashboard.appendChild(TableRow[Column]);
	j = 0;
	for (var i = 0; i < sessionStorage.length; i++){
		if (sessionStorage.key(i).substr(0,4) == 'dash') {
			TableCell[i] = document.createElement("td");
			TableCell[i].className = "dashboard_placeholder";
			TableCell[i].id=sessionStorage.key(i);
			TableRow[Column].appendChild(TableCell[i]);
			UpdateApplet(sessionStorage.getItem(sessionStorage.key(i)), TableCell[i])
			if ((j == 2) || (j == 5)) {
				Column++;
				TableRow[Column] = document.createElement("tr");
				TableDashboard.appendChild(TableRow[Column]);
			}
			j++;
		}
	}
	setInterval(UpdateDashboard, 30000)
}

function UpdateApplet(Target, Element) {
	var PostData='';
	Target='dashboard/'+Target;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			Element.innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST",Target,false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Pragma","no-cache");
	xmlhttp.send(PostData);
	return false;
}

function UpdateDashboard() {
	for (var i = 0; i < sessionStorage.length; i++){
		Cell=document.getElementById(sessionStorage.key(i));
		UpdateApplet(sessionStorage.getItem(sessionStorage.key(i)), Cell)
	}
}

function UpdateServer(id) {
	Target="Dashboard.php";
	var PostData='';
	Target=Target+"?Update=Yes&ID="+id;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
//			sessionStorage.setItem('ScripName', xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET",Target,false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Pragma","no-cache");
	xmlhttp.send(PostData);
	return false;
}

function AddToSelect(id, Title) {
	var x = document.getElementById("dashboard_options");
	var option = document.createElement("option");
	option.text = Title;
	x.add(option, id);
}

function RemoveFromSelect() {
	var x = document.getElementById("dashboard_options");
	x.remove(x.selectedIndex);
}

function RemoveApplet(id, Title) {
	sessionStorage.removeItem("dashboard"+id);
	document.body.removeChild(TableDashboard);
	ShowDashboard();
	AddToSelect(id, Title);
	UpdateServer(id);
}

function AddApplet() {
	var x = document.getElementById("dashboard_options");
	id=x.value;
	sessionStorage.setItem("dashboard"+id, sessionStorage.getItem("scripts"+id));
	document.body.removeChild(TableDashboard);
	ShowDashboard();
	UpdateServer(id);
	RemoveFromSelect();
}