#!/usr/bin/tclsh
 
# Init directory
set rootDir [file dirname [file dirname [info script]]]
set logDir $rootDir
set serveurLogFileName [file join $rootDir serverLog serveurLog.tcl]

puts "Starting cultiPi"
set TimeStartcultiPi [clock milliseconds]
if {[lindex $argv 0] != ""} {
    set serveurLogFileName [lindex $argv 0]
}
puts "Log file $serveurLogFileName"

# Port number
set port(server) 6000
set port(serverLogs) 6001
set port(serverI2C) 6002

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer

# Source files
source [file join $rootDir cultiPi src stop.tcl]
source [file join $rootDir cultiPi src serveurMessage.tcl]

# Load serverLog
puts "Starting serveurLog"
set TimeStartserveurLog [clock milliseconds]
open "| tclsh \"$serveurLogFileName\" $port(serverLogs) \"$logDir\""

# init log
after 100
::piLog::openLog $port(serverLogs) "culipi"
after 100
::piLog::log $TimeStartcultiPi "info" "starting serveur"
::piLog::log $TimeStartserveurLog "info" "starting serveurLog"

# Load server Culti Pi
::piLog::log [clock millisecond] "info" "starting serveur"
::piServer::start messageGestion $port(server)
::piLog::log [clock millisecond] "info" "serveur is started"


# Lancement du serveur I2C
puts "Starting serverI2C"
::piLog::log [clock milliseconds] "info" "Load serverI2C"
set serveurLogFileName [file join $rootDir serverI2C serverI2C.tcl]
open "| tclsh \"$serveurLogFileName\" $port(serverI2C)"


vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"