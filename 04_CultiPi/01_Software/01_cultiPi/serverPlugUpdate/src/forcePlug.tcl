
# Cette procédure est utilisée pour déforcer une prise dans un état
proc unForcePlug {plugNumber} {
    set ::plug($plugNumber,source) "plugv"
    set ::plug($plugNumber,force,value) ""
    set ::plug($plugNumber,force,idAfterProc) ""
    
    ::piLog::log [clock milliseconds] "info" "Unforce plug $plugNumber"
    
    # On appel la procédure pour mettre à jour la prise
    ::updatePlug $plugNumber

}