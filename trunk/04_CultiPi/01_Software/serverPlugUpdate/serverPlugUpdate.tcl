# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(server)    [lindex $argv 0]
set confXML         [lindex $argv 1]
set port(serverLogs) [lindex $argv 2]

# Global var for regulation
set regul(alarme) 0

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer

source [file join $rootDir serverPlugUpdate src emeteur.tcl]

::piLog::openLog $port(serverLogs) "serverPlugUpdate"
::piLog::log [clock milliseconds] "info" "starting serverPlugUpdate"


proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv -$message-"
}

# Load server
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {
    switch [lindex $message 1] {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Ask stop serverPlugUpdate"
            stopIt
        }
        default {
            ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
        }
    }
}
::piServer::start messageGestion $port(server)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Start stopping serverPlugUpdate"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "End stopping serverPlugUpdate"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

# Load plug adress
set confPath [file dirname $confXML]
set plugaFileName [file join $confPath pluga]
::piLog::log [clock milliseconds] "info" "pluga filename : $plugaFileName"
set fid [open [file join $confPath plg pluga] r]
set nbPlug 0
while {[eof $fid] != 1} {
    gets $fid OneLine
    if {$OneLine != "" && $nbPlug != 0} {
        set plug($nbPlug,adress) $OneLine
    }
    if {$OneLine != "" } {
        incr nbPlug
    }
}
close $fid

# Load plug parameters
for {set i 1} {$i < $nbPlug} {incr i} {
    puts [file join $confPath plg "plug[string map {" " "0"} [format %2.f $i]]"]
    set fid [open [file join $confPath plg "plug[string map {" " "0"} [format %2.f $i]]"] r]
    while {[eof $fid] != 1} {
        gets $fid OneLine
        switch [string range $OneLine 0 3] {
            "REG:" {
                set plug($nbPlug,REG,type) [string index $OneLine 4] 
                set plug($nbPlug,REG,sens) [string index $OneLine 5]
                set plug($nbPlug,REG,precision) [expr [string range $OneLine 6 8] / 10.0]
            }
            "SEC:" {
                set plug($nbPlug,SEC,type) [string index $OneLine 4] 
                set plug($nbPlug,SEC,sens) [string index $OneLine 5]
                set plug($nbPlug,SEC,etat_prise) [string index $OneLine 6]
                set plug($nbPlug,SEC,value) [expr [string range $OneLine 7 9] / 10.0]
            }
            "SEN:" {
                set plug($nbPlug,calcul,type) [string index $OneLine 4] 
                set plug($nbPlug,calcul,capteur_1) [string index $OneLine 5]
                set plug($nbPlug,calcul,capteur_2) [string index $OneLine 6] 
                set plug($nbPlug,calcul,capteur_3) [string index $OneLine 7] 
                set plug($nbPlug,calcul,capteur_4) [string index $OneLine 8] 
                set plug($nbPlug,calcul,capteur_5) [string index $OneLine 9] 
                set plug($nbPlug,calcul,capteur_6) [string index $OneLine 10] 
            }
            "STOL:" {
                set plug($nbPlug,SEC,precision) [expr [string range $OneLine 5 7] / 10.0]
            }
        }
    }
    close $fid
}

# initialisation de la partie émetteur
emeteur_init

emeteur_update_loop

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\serverPlugUpdate\serverPlugUpdate.tcl" 6003 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverPlugUpdate\conf.xml" 6001 