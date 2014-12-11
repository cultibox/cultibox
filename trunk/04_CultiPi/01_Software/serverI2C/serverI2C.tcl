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
package require piTools

::piLog::openLog $port(serverLogs) "serverI2C"
::piLog::log [clock milliseconds] "info" "starting serverI2C - PID : [pid]"

proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv - pid [pid] -$message-"
}

# Load server I2C
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    switch $commande {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Ask stop serverI2C"
            stopIt
        }
        "pid" {
            ::piLog::log [clock milliseconds] "info" "Asked pid"
            ::piServer::sendToServer $serverForResponse "$::port(server) $indexForResponse pid serverPlugUpdate [pid]"
        }
        "getI2C" {
            set slaveAdress [::piTools::lindexRobust $message 3]
            set slaveRegister [::piTools::lindexRobust $message 4]
            ::piLog::log [clock milliseconds] "debug" "$serverForResponse commande $indexForResponse ask I2C value of slave $slaveAdress adress $slaveRegister"
            
            # On execute la commande
            set value ""
            set rc [catch {
                set value [exec i2cset -y 1 $slaveAdress $slaveRegister $slaveValue]
            } msg]
            if {$rc == 1} {
                # Trame standard : [FROM] [INDEX] [commande] [argument]
                ::piLog::log [clock milliseconds] "erreur" "erreur exec i2cget -y 1 $slaveAdress $slaveRegister - erreur : -$msg- "
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse DEFCOM $msg"
            } else {
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse OK $value"
            }
            
        }
        "setI2C" {
            set slaveAdress [::piTools::lindexRobust $message 3]
            set slaveRegister [::piTools::lindexRobust $message 4]
            set slaveValue [::piTools::lindexRobust $message 5]
            ::piLog::log [clock milliseconds] "debug" "$serverForResponse commande $indexForResponse ask I2C to set value of slave $slaveAdress adress $slaveRegister value $slaveValue"

            # On execute la commande
            set rc [catch {
                exec i2cset -y 1 $slaveAdress $slaveRegister $slaveValue
            } msg]
            if {$rc == 1} {
                ::piLog::log [clock milliseconds] "erreur" "erreur exec i2cset -y 1 $slaveAdress $slaveRegister $slaveValue - erreur : -$msg- "
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse DEFCOM $msg"
            } else {
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse OK"
            }

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