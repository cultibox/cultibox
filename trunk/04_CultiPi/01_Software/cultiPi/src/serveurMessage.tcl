
proc messageGestion {message} {
    if {$message == "stop"} {
        ::piLog::log [clock milliseconds] "info" "Demande Arret de Culti Pi"
        stopCultiPi
    } else {
        ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
    }
}
