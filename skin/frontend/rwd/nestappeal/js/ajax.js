var http_request = false;

function ajaxctntme(url, parameters,divid) {
  http_request = false;
  
  if (window.XMLHttpRequest) { // Mozilla, Safari,...
	 http_request = new XMLHttpRequest();
	 if (http_request.overrideMimeType) {
		// set type accordingly to anticipated content type
		//http_request.overrideMimeType('text/xml');
		http_request.overrideMimeType('text/html');
	 }
  } else if (window.ActiveXObject) { // IE
	 try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
		try {
		   http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	 }
  }
  if (!http_request) {
	 alert('Cannot create XMLHTTP instance');
	 return false;
  }
  http_request.onreadystatechange = function(){ alertContents_ajax1(divid); };
  if (parameters != "")
	url = url + '?' + parameters + '?' + divid;
	//alert(url);
  http_request.open('POST', url, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http_request.setRequestHeader("Content-length", parameters.length);
  http_request.setRequestHeader("Connection", "close");
  http_request.send(parameters);
}

function alertContents_ajax1(divid) {	
		nmvb=divid;
		
	if (http_request.readyState == 1) {
	 
		document.getElementById('loader').style.display = "block";
	}	
		
 	if (http_request.readyState == 4) {
	 
		result = http_request.responseText;		
		document.getElementById('loader').style.display = "none";		
		document.getElementById(nmvb).style.display = "block";
		document.getElementById(nmvb).innerHTML = ""; 
		document.getElementById(nmvb).innerHTML = result; 
	}
	//Shadowbox.load();
}
