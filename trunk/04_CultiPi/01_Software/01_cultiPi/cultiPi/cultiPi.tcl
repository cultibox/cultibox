#!/usr/bin/tclsh
 
# Init directory
set rootDir [file dirname [file dirname [info script]]]
set logDir $rootDir
set serveurLogFileName [file join $rootDir serverLog serveurLog.tcl]

puts "Starting cultiPi - PID : [pid]"
set TimeStartcultiPi [clock milliseconds]

set fileName(cultiPi,confRootDir) [lindex $argv 0]
puts "server : XML Conf directory : $fileName(cultiPi,confRootDir)"
if {$fileName(cultiPi,confRootDir) == ""} {
    puts "CultiPi : Error : conf directory must be defined"
    return
}
set fileName(cultiPi,conf) [file join $fileName(cultiPi,confRootDir) conf.xml]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piXML
package require piTools

# Source files
source [file join $rootDir cultiPi src stop.tcl]
source [file join $rootDir cultiPi src serveurMessage.tcl]
source [file join $rootDir cultiPi src searchSocket.tcl]

# Port number
set port(server) [findAvailableSocket 6000]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

# Chargement du fichier qui donne la configuration
set fileName(cultiPi,confDir) [file join $fileName(cultiPi,confRootDir) [lindex [::piXML::open_xml $fileName(cultiPi,conf)] 2 0 1 1]]
puts "CultiPi : conf : conf to start is $fileName(cultiPi,confDir) - File exists ? [file exists $fileName(cultiPi,confDir)]"

# Load cultiPi configuration
set confStart(start) [lindex [::piXML::open_xml [file join $fileName(cultiPi,confDir) cultiPi start.xml]] 2]


# Load serverLog
puts "CultiPi : start : Starting serveurLog"
set confStart(serverLog,pid) ""
set confStart(serverLog) [::piXML::searchItemByName serverLog $confStart(start)]
set confStart(serverLog,pathexe) [::piXML::searchOptionInElement pathexe $confStart(serverLog)]
puts "CultiPi : start : serveurLog pathexe : $confStart(serverLog,pathexe)"
set confStart(serverLog,path) [file join $rootDir [::piXML::searchOptionInElement path $confStart(serverLog)]]
puts "CultiPi : start : serveurLog path : $confStart(serverLog,path)"
set confStart(serverLog,xmlconf) [file join $fileName(cultiPi,confDir) [::piXML::searchOptionInElement xmlconf $confStart(serverLog)]]
puts "CultiPi : start : serveurLog xmlconf : $confStart(serverLog,xmlconf) , file exists ? [file exists $confStart(serverLog,xmlconf)]"
set confStart(serverLog,waitAfterUS) [::piXML::searchOptionInElement waitAfterUS $confStart(serverLog)]
puts "CultiPi : start : serveurLog waitAfterUS : $confStart(serverLog,waitAfterUS)"
set confStart(serverLog,port) [::piXML::searchOptionInElement port $confStart(serverLog)]
puts "CultiPi : start : serveurLog port : $confStart(serverLog,port)"
set tempLogPath [::piXML::searchItemByName logPath [lindex [::piXML::open_xml $confStart(serverLog,xmlconf)] 2]]
set confStart(serverLog,logsRootDir) [::piXML::searchOptionInElement logfile $tempLogPath]
puts "CultiPi : start : serveurLog logsRootDir : $confStart(serverLog,logsRootDir)"
set TimeStartserveurLog [clock milliseconds]
#open "| tclsh \"$serveurLogFileName\" $port(serverLogs) \"$logDir\""
puts "CultiPi : start : serveurLog : $confStart(serverLog,pathexe) \"$confStart(serverLog,path)\" $confStart(serverLog,port) \"$confStart(serverLog,logsRootDir)\""

set confStart(serverLog,pipeID) [open "| $confStart(serverLog,pathexe) \"$confStart(serverLog,path)\" $confStart(serverLog,port) \"$confStart(serverLog,logsRootDir)\""]
after $confStart(serverLog,waitAfterUS)

# init log
::piLog::openLog $confStart(serverLog,port) "culipi"
::piLog::log $TimeStartcultiPi "info" "starting serveur"
::piLog::log $TimeStartserveurLog "info" "starting serveurLog"
::piLog::log $TimeStartserveurLog "info" "Port : $port(server)"

# Load server Culti Pi
::piLog::log [clock millisecond] "info" "starting serveur"
::piServer::start messageGestion $port(server)
::piLog::log [clock millisecond] "info" "serveur is started"

# On change la vitesse du bus I2C
set RC [catch {
    exec sudo modprobe -r i2c_bcm2708
    exec sudo modprobe i2c_bcm2708 baudrate=32000
} msg]
if {$RC != 0} {
    puts "CultiPi : I2C speed : error $msg"
}


# Lancement de tous les modules
foreach moduleXML $confStart(start) {
    set moduleName [::piXML::searchOptionInElement name $moduleXML]
    if {$moduleName != "serverLog"} {
        set confStart(${moduleName},pid) ""
        set confStart(${moduleName},pathexe) [::piXML::searchOptionInElement pathexe $moduleXML]
        puts "CultiPi : start : $moduleName pathexe : $confStart(${moduleName},pathexe)"
        set confStart($moduleName,path) [file join $rootDir [::piXML::searchOptionInElement path $moduleXML]]
        puts "CultiPi : start : $moduleName path : $confStart($moduleName,path)"
        set confStart($moduleName,xmlconf) [file join $fileName(cultiPi,confDir) [::piXML::searchOptionInElement xmlconf $moduleXML]]
        puts "CultiPi : start : $moduleName xmlconf : $confStart($moduleName,xmlconf) , file exists ? [file exists $confStart($moduleName,xmlconf)]"
        set confStart($moduleName,waitAfterUS) [::piXML::searchOptionInElement waitAfterUS $moduleXML]
        puts "CultiPi : start : $moduleName waitAfterUS : $confStart($moduleName,waitAfterUS)"
        set confStart($moduleName,port) [::piXML::searchOptionInElement port $moduleXML]
        puts "CultiPi : start : $moduleName port : $confStart($moduleName,port)"

        ::piLog::log [clock milliseconds] "info" "Load $moduleName"
        puts "CultiPi : start : $moduleName exec : $confStart($moduleName,pathexe) \"$confStart($moduleName,path)\" $confStart($moduleName,port) \"$confStart($moduleName,xmlconf)\" $confStart(serverLog,port) $port(server)"
        set confStart($moduleName,pipeID) [open "| $confStart($moduleName,pathexe) \"$confStart($moduleName,path)\" $confStart($moduleName,port) \"$confStart($moduleName,xmlconf)\" $confStart(serverLog,port) $port(server)"]
        after $confStart($moduleName,waitAfterUS)
               
    }
}

# On attend que tous les modules ait démarré
proc askPid {} {
    foreach moduleXML $::confStart(start) {
        set moduleName [::piXML::searchOptionInElement name $moduleXML]
        if {$moduleName != "serverLog"} {
            # on lui demande son PID
            # Trame standard : [FROM] [INDEX] [commande] [argument]
            ::piServer::sendToServer $::confStart($moduleName,port) "$::port(server) [incr ::TrameIndex] pid"
        }
    }
}
after 5000 askPid

proc updateRepere {} {

    # pour chaque repère, on met à jour la valeur dans le serveur

    
    after 1000 updateRepere

}

updateRepere

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\01_cultiPi\cultiPi\cultiPi.tcl" "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf"

# Linux start
# tclsh /home/pi/cultipi/01_Software/01_cultiPi/cultiPi/cultiPi.tcl /home/pi/cultipi/02_conf