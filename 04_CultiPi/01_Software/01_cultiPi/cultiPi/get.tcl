#!/usr/bin/tclsh

# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

set port(serverGet) [::piServer::findAvailableSocket 6022]
set port(serverCultipi) 6000
set port(serverAcqSensor) 6006
set port(serverPlugUpdate) 6004
set port(serverHisto) 6009

::piLog::openLogAs "none"

set module   [lindex $argv 0]
set adresseIP [lindex $argv 1]

#puts "Reading variable [lrange $argv 1 [expr $argc - 1]] of module $module"

set killID ""

proc messageGestion {message host} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    puts [join [lrange $message 3 end] "\t"]

    # On supprime le killID
    after cancel $::killID
    
    set ::forever 1
}
::piServer::start messageGestion $port(serverGet)

# On regarde sur quel serveur il souhaite lancer la commande

# Demande lecture du repere
# Trame standard : [FROM] [INDEX] [commande] [argument]
::piServer::sendToServer $port($module) "$port(serverGet) 0 getRepere [lrange $argv 2 [expr $argc - 1]]" $adresseIP

# Après 2 secondes, s'il n'a pas répondu on le tue
set killID [after 2000 {
    set ::forever 1
    puts "TIMEOUT"
}]

vwait forever

# tclsh /opt/cultipi/cultiPi/get.tcl serverAcqSensor localhost "::sensor(1,value)" "::sensor(2,value)"
# tclsh /opt/cultipi/cultiPi/get.tcl serverPlugUpdate localhost "::plug(1,value)" "::plug(2,value)"
# tclsh /opt/cultipi/cultiPi/get.tcl serverAcqSensor localhost "::sensor(firsReadDone)" 
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\get.tcl" serverAcqSensor 192.168.0.100 "::sensor(1,value)"
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\get.tcl" serverAcqSensor localhost "::sensor(1,value)"
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\get.tcl" serverAcqSensor localhost "::sensor(1,value)" "::sensor(2,value)"
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\get.tcl" serverCultipi localhost "statusInitialisation"
# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\get.tcl" serverCultipi localhost  statusInitialisation cultipiActualHour
# tclsh /home/sdf/Bureau/Cultipi/cultiPi/get.tcl serverAcqSensor 192.168.1.55 "::sensor(1,value)"