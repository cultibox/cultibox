# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Port number
set port(serverLogs) 6001
set port(serverI2C) 6002

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer

::piLog::openLog $port(serverLogs) "serverI2C"
::piLog::log [clock milliseconds] "info" "starting serverI2C"

if {0} {
proc bgerror {message} {
    ::piLog::log "<[clock milliseconds]><serveurlog><erreur_critique><bgerror in $::argv -$message->"
}
}

# Load server I2C
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {
    switch [lindex $message 1] {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Demande Arrêt de serverI2C"
            stopIt
        }
        "getI2C" {
            ::piLog::log [clock milliseconds] "debug" "[lindex $message 1] ask I2C value of slave [lindex $message 2] adress [lindex $message 3]"
        }
        "setI2C" {
            ::piLog::log [clock milliseconds] "debug" "[lindex $message 1] ask I2C to set value of slave [lindex $message 2] adress [lindex $message 3] value [lindex $message 4]"
        }
        default {
            ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
        }
    }
}
::piServer::start messageGestion $port(serverI2C)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Début arrêt serverI2C"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "Fin arrêt serverI2C"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"