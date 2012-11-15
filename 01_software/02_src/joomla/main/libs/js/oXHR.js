/* ** cartouche ********************************************************************* */
/* Script complet de gestion d'une requête de type XMLHttpRequest                     */
/* Par Sébastien de la Marck (aka Thunderseb)                                         */
/* ********************************************************************************** */

function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}



function sendXMLHttpRequest(xhr,infos) {
    var sDate = encodeURIComponent(new Date());
    var sLog = encodeURIComponent("pouet");
    var sIP = encodeURIComponent(infos);

    xhr.timeout = 4000;
    xhr.open("GET", "http://www.cbx.greenbox-botanic.com/index.php?date=" + sDate + "&log=" + sLog + "&ip=" + sIP, true);
    xhr.send(null);
}
