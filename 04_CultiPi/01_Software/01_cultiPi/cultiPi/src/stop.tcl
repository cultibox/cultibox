
proc stopCultiPi {} {
    ::piLog::log [clock milliseconds] "info" "Debut arret Culti Pi"
    
    ::piLog::log [clock milliseconds] "info" "Extinction des alimentations"
    exec gpio -g write 18 0

    # Arret de tous les modules
    foreach moduleXML $::confStart(start) {
        set moduleName [::piXML::searchOptionInElement name $moduleXML]
        if {$moduleName != "serverLog"} {
            ::piLog::log [clock milliseconds] "info" "Demande arret $moduleName"
            # Arret du module
            ::piServer::sendToServer $::confStart($moduleName,port) "[clock milliseconds] 000 stop"
            
            #on attend 200 ms
            after 200
            
            # try to kill
            catch {
                ::piLog::log [clock milliseconds] "info" "Try to kill $moduleName pid $::confStart($moduleName,pid)"
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
    ::piServer::sendToServer $::confStart(serverLog,port) "<[clock milliseconds]><cultipi><debug><stop>"
    ::piLog::closeLog
    
    after 500 {
        puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiPi : Bye Bye ! "
        set ::forever 0
    }
}
