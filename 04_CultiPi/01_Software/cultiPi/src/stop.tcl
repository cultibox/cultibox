
proc stopCultiPi {} {
    ::piLog::log [clock milliseconds] "info" "Debut arret Culti Pi"

    # Arret de tous les modules
    foreach moduleXML $::confStart(start) {
        set moduleName [::piXML::searchOptionInElement name $moduleXML]
        if {$moduleName != "serverLog"} {
            ::piLog::log [clock milliseconds] "info" "Demande arret $moduleName"
            # Arret du module
            ::piServer::sendToServer $::confStart($moduleName,port) "stop"
            
            #on attend 200 ms
            after 200
            
            # try to kill
            catch {
                # Kill for windows
                if {$::tcl_platform(platform) == "windows"} {
                    exec exec [auto_execok taskkill] /PID $::confStart($moduleName,pid)
                }
                # Kill for linux
                if {$::tcl_platform(platform) == "unix"} {
                    exec kill $::confStart($moduleName,pid)
                }
            }
        }
    }

    ::piLog::log [clock milliseconds] "info" "Fin arret Culti Pi"
    
    # Arrêt du serveur de log (forcement en dernier)
    ::piServer::sendToServer $::port(serverLogs) "stop"
    ::piLog::closeLog
    
    after 500 {set ::forever 0}
}
