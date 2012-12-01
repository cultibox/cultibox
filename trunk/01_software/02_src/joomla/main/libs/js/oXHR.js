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
    var sDate = getTimestamp();
    var sLog = encodeURIComponent("pouet");
    var sIP = encodeURIComponent(value_os);
    var sVersion = encodeURIComponent(value_version);


    xhr.open("GET", "http://www.cbx.greenbox-botanic.com/index.php?date=" + sDate + "&log=" + sLog + "&ip=" + sIP + "&cbx_soft_version=" + sVersion, true);
    xhr.send(null);
}


function getTimestamp() {
   var dt = new Date();

    var year = dt.getFullYear();

    var month = dt.getMonth() + 1 ;
    if(month<10) month="0"+month;
    
    var day = dt.getDate();
    if(day<10) day="0"+day;

    var second = dt.getSeconds();
    var minute = dt.getMinutes();
    var hour = dt.getHours();

    var tmpDate = year + "" + month + "" + day + "" + hour + "" + minute + "" + second ;
    return tmpDate;
}
