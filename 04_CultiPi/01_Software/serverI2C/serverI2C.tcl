# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(server) [lindex $argv 0]
set confXML         [lindex $argv 1]
set port(serverLogs) [lindex $argv 2]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer

::piLog::openLog $port(serverLogs) "serverI2C"
::piLog::log [clock milliseconds] "info" "starting serverI2C"

proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv -$message-"
}

# Load server I2C
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {
    switch [lindex $message 1] {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Ask stop serverI2C"
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
::piServer::start messageGestion $port(server)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Start stopping serverI2C"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "End stopping serverI2C"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"