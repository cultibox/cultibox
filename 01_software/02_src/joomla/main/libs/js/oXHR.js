/* ** cartouche ********************************************************************* */
/* Script complet de gestion d'une requête de type XMLHttpRequest                     */
/* Par Sébastien de la Marck (aka Thunderseb)                                         */
/* ********************************************************************************** */

function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest)
        return new XMLHttpRequest();
 
    if (window.ActiveXObject)
    {
        var names = [
            "Msxml2.XMLHTTP.6.0",
            "Msxml2.XMLHTTP.3.0",
            "Msxml2.XMLHTTP",
            "Microsoft.XMLHTTP"
        ];
        for(var i in names)
        {
            try{ return new ActiveXObject(names[i]); }
            catch(e){}
        }
    }
	return null;
}



function sendXMLHttpRequest(xhr,value_os,value_version) {
    var sDate = encodeURIComponent(new Date());
    var sLog = encodeURIComponent("pouet");
    var sIP = encodeURIComponent(value_os);
    var sVersion = encodeURIComponent(value_version);

    //xhr.timeout = 4000;
    xhr.open("GET", "http://www.cbx.greenbox-botanic.com/index.php?date=" + sDate + "&log=" + sLog + "&ip=" + sIP + "&cbx_soft_version=" + sVersion, true);
    xhr.send(null);
}
