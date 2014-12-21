#!/usr/bin/tclsh

# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

set port(serverViewRepere) 6022
set port(serverLog) 6003
set port(serverCultipi) 6000
set port(serverAcqSensor) 6006
set port(serverPlugUpdate) 6004
set port(serverHisto) 6009

::piLog::openLog $port(serverLog) "cultiPiviewRepere"

set module   [lindex $argv 0]
set variable [lindex $argv 1]

puts "Reading variable $variable of module $module"

proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    puts $message

    set ::forever 1
}
::piServer::start messageGestion $port(serverViewRepere)

# On regarde sur quel serveur il souhaite lancer la commande

# Demande lecture du repere
# Trame standard : [FROM] [INDEX] [commande] [argument]
::piServer::sendToServer $port($module) "$port(serverViewRepere) 0 getRepere $variable"


vwait forever

# tclsh /opt/cultipi/cultiPi/cultiPiviewRepere.tcl serverAcqSensor "::sensor(1,value)"
# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"