# Init directory
set rootDir [file dirname [file dirname [info script]]]
set logDir $rootDir

puts "Starting cultiPi"
set TimeStartcultiPi [clock milliseconds]

# Port number
set port(server) 6000
set port(serverLogs) 6001
set port(serverI2C) 6002

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer

# Load serverLog
puts "Starting serveurLog"
set TimeStartserveurLog [clock milliseconds]
set serveurLogFileName [file join $rootDir serverLog serveurLog.tcl]
open "| tclsh \"$serveurLogFileName\" $port(serverLogs) \"$logDir\""

# init log
after 100
::piLog::openLog $port(serverLogs) "culipi"
after 100
::piLog::log $TimeStartcultiPi "info" "starting serveur"
::piLog::log $TimeStartserveurLog "info" "starting serveurLog"

# Load server Culti Pi
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {
    if {$message == "stop"} {
        ::piLog::log [clock milliseconds] "info" "Demande Arret de Culti Pi"
        stopCultiPi
    } else {
        ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
    }
}
::piServer::start messageGestion $port(server)
::piLog::log [clock millisecond] "info" "serveur is started"

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

::piLog::log [clock milliseconds] "info" "Load serverI2C"
set serveurLogFileName [file join $rootDir serverI2C serverI2C.tcl]
#open "| tclsh \"$serveurLogFileName\" $port(serverI2C)"


vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"