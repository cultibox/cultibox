#!/usr/bin/tclsh

# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

set port(serverSet) [::piServer::findAvailableSocket 6024]
set port(serverCultipi) 6000
set port(serverAcqSensor) 6006
set port(serverPlugUpdate) 6004
set port(serverHisto) 6009

::piLog::openLogAs "none"

set module   [lindex $argv 0]
set variable [lindex $argv 1]
set adresseIP [lindex $argv 2]

#puts "Reading variable [lrange $argv 1 [expr $argc - 1]] of module $module"


# Demande d'écriture du repere
# Trame standard : [FROM] [INDEX] [commande] [argument]
::piServer::sendToServer $port($module) "$port(serverSet) 0 setRepere [lrange $argv 1 [expr $argc - 1]]" $adresseIP


# tclsh /opt/cultipi/cultiPi/set.tcl serverPlugUpdate 1 on 20
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\set.tcl" serverPlugUpdate 1 on 10
