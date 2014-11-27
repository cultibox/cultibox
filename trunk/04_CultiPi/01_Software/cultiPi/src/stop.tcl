
proc stopCultiPi {} {
    ::piLog::log [clock milliseconds] "info" "D�but arr�t Culti Pi"

    # Arret du serveur I2C
    ::piServer::sendToServer $::port(serverI2C) "stop"
    
    ::piLog::log [clock milliseconds] "info" "Fin arr�t Culti Pi"
    
    # Arr�t du serveur de log (forcement en dernier)
    ::piServer::sendToServer $::port(serverLogs) "stop"
    ::piLog::closeLog
    
    after 500 {set ::forever 0}
}
