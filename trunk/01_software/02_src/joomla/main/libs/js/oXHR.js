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

function sendXMLHttpRequest(xhr,value_os,value_version,nb_reboot,last_reboot,cbx_id,firm_version,emeteur_version,sensor_version,log) {
    var sDate = getTimestamp();
    var sIP = encodeURIComponent(value_os);
    var sLog = encodeURIComponent(log);
    var sVersion = encodeURIComponent(value_version);
    var sNB = encodeURIComponent(nb_reboot);
    var sLast = encodeURIComponent(last_reboot);
    var sID = encodeURIComponent(cbx_id);
    var sFirm = encodeURIComponent(firm_version);
    var sEmet = encodeURIComponent(emeteur_version);
    var sSen = encodeURIComponent(sensor_version);

    xhr.open("GET", "http://www.cbx.greenbox-botanic.com/index.php?date=" + sDate + "&log=" + sLog + "&ip=" + sIP + "&cbx_soft_version=" + sVersion + "&cbx_id=" + sID  + "&cbx_firmware=" + sFirm + "&cbx_emetor_firmware=" + sEmet + "&cbx_sht_firmware=" + sSen + "&cbx_nbreboot=" + sNB + "&cbx_lastreboot=" + sLast, true);
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
    if(second<10) second="0"+second;

    var minute = dt.getMinutes();
    if(minute<10) minute="0"+minute;

    var hour = dt.getHours();
    if(hour<10) hour="0"+hour;

    var tmpDate = year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second ;
    return tmpDate;
}
