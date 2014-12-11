#!/usr/bin/tclsh

# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

set port(server) 6022

::piLog::openLog 6001 "cultiPiviewRepere"

set repere [lindex $argv 0]

puts "Reading repere $repere"

proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    puts $message

}
::piServer::start messageGestion $port(server)


# Demande lecture du repere
# Trame standard : [FROM] [INDEX] [commande] [argument]
while {1} {
    ::piServer::sendToServer 6004 "$port(server) 0 getRepere $repere updateStatus"
    
    after 1000
}


# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"