
proc stopCultiPi {} {
    ::piLog::log [clock milliseconds] "info" "Début arrêt Culti Pi"

    # Arret du serveur I2C
    ::piServer::sendToServer $::port(serverI2C) "stop"
    
    ::piLog::log [clock milliseconds] "info" "Fin arrêt Culti Pi"
    
    # Arrêt du serveur de log (forcement en dernier)
    ::piServer::sendToServer $::port(serverLogs) "stop"
    ::piLog::closeLog
    
    after 500 {set ::forever 0}
}
