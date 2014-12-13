# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverPlugUpdate)  [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]
set port(serverI2C)         [lindex $argv 3]

# Global var for regulation
set regul(alarme) 0

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

# Source extern files
source [file join $rootDir serverPlugUpdate src emeteur.tcl]
source [file join $rootDir serverPlugUpdate src pluga.tcl]
source [file join $rootDir serverPlugUpdate src module_wireless.tcl]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

::piLog::openLog $port(serverLogs) "serverPlugUpdate"
::piLog::log [clock milliseconds] "info" "starting serverPlugUpdate - PID : [pid]"


proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv - pid [pid] -$message-"
}

# Load server
::piLog::log [clock millisecond] "info" "starting serveur"
proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    switch ${commande} {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Asked stop"
            stopIt
        }
        "pid" {
            ::piLog::log [clock milliseconds] "info" "Asked pid"
            ::piServer::sendToServer $serverForResponse "$::port(serverPlugUpdate) $indexForResponse pid serverPlugUpdate [pid]"
        }
        "getRepere" {
            # Le repere est le numéro de prise
            set repere [::piTools::lindexRobust $message 3]
            set parametre [::piTools::lindexRobust $message 4]
            ::piLog::log [clock milliseconds] "info" "Asked getRepere $repere - parametre $parametre"
            # Les parametres d'un repere : nom Valeur 
            
            if {[array names ::plug -exact "$repere,$parametre"] != ""} {
                ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse getRepere $::plug($repere,$parametre)"
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getRepere $::plug($repere,$parametre)"
            } else {
                ::piLog::log [clock milliseconds] "error" "Asked getRepere $repere - parametre $parametre not recognize"
            }
        }
        default {
            # Si on reçoit le retour d'une commande, le nom du serveur est le notre
            if {$serverForResponse == $::port(serverPlugUpdate)} {
            
                if {[array names ::TrameSended -exact $indexForResponse] != ""} {
                    
                    switch [lindex $::TrameSended($indexForResponse) 0] {
                        "update_plug_value" {
                            set plugumber [lindex $::TrameSended($indexForResponse) 1]
                        
                            set ::plug($plugumber,updateStatus) $commande
                            set ::plug($plugumber,updateStatusComment) ${message}
                        
                            ::piLog::log [clock milliseconds] "info" "I2C Update plug $plugumber updateStatus : -$commande- updateStatusComment : -${message}-"
                        
                            # On supprime cette donnée de la mémoire
                            unset ::TrameSended($indexForResponse)
                        }
                        default {
                            ::piLog::log [clock milliseconds] "erreur" "Not recognize keyword response -${message}-"
                        }                    
                    }
                    
                } else {
                    ::piLog::log [clock milliseconds] "erreur" "Not requested response -${message}-"
                }
            
                
            } else {
                ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
            }
        }
    }
}
::piServer::start messageGestion $port(serverPlugUpdate)
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
set plugaFileName [file join $confPath plg pluga]

# On démarre le chip de l'emmeteur
set status "retry_needed"
while {$status == "retry_needed"} {
    set status [::wireless::outFromBootloader]
    after 100
}

# Parse pluga filename and send adress to module if needed
set nbPlug [readPluga $plugaFileName]


# Load plug parameters
for {set i 1} {$i < $nbPlug} {incr i} {

    set plugXXFilename [file join $confPath plg "plug[string map {" " "0"} [format %2.f $i]]"]
    
    # On vérifie la présence du fichier
    if {[file exists $plugXXFilename] != 1} {
        ::piLog::log [clock milliseconds] "error" "File $plugXXFilename does not exists"
        break;
    } else {
        ::piLog::log [clock milliseconds] "info" "reading plugXX $plugXXFilename"
        set fid [open $plugXXFilename r]
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

}

# initialisation de la partie émetteur
::piLog::log [clock milliseconds] "info" "emeteur_init"
emeteur_init

# Boucle de régulation
::piLog::log [clock milliseconds] "info" "emeteur_update_loop"
emeteur_update_loop

# Une fois la boucle de régulation démarrée , on peut activer le pilotage des prises
::wireless::start

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\serverPlugUpdate\serverPlugUpdate.tcl" 6003 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverPlugUpdate\conf.xml" 6001 