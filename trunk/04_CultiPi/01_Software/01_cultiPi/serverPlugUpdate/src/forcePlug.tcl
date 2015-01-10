
# Cette proc�dure est utilis�e pour d�forcer une prise dans un �tat
proc unForcePlug {plugNumber} {
    set ::plug($plugNumber,source) "plugv"
    set ::plug($plugNumber,force,value) ""
    set ::plug($plugNumber,force,idAfterProc) ""
    
    ::piLog::log [clock milliseconds] "info" "Unforce plug $plugNumber"
    
    # On appel la proc�dure pour mettre � jour la prise
    ::updatePlug $plugNumber

}