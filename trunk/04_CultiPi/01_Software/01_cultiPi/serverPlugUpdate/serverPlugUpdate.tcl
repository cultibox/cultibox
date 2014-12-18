# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverPlugUpdate)  [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]
set port(serverCultiPi)     [lindex $argv 3]

# Global var for regulation
set regul(alarme) 0
# Variable qui mémorise les prise qui ont été mises à jour
set plug(updated) ""

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

# Chargement des fichiers externes
source [file join $rootDir serverPlugUpdate src emeteur.tcl]
source [file join $rootDir serverPlugUpdate src pluga.tcl]
source [file join $rootDir serverPlugUpdate src module_wireless.tcl]
source [file join $rootDir serverPlugUpdate src sensor.tcl]
source [file join $rootDir serverPlugUpdate src serveurMessage.tcl]
source [file join $rootDir serverPlugUpdate src regulation.tcl]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

::piLog::openLog $port(serverLogs) "serverPlugUpdate"
::piLog::log [clock milliseconds] "info" "starting serverPlugUpdate - PID : [pid]"
::piLog::log [clock milliseconds] "info" "port serverPlugUpdate : $port(serverPlugUpdate)"
::piLog::log [clock milliseconds] "info" "confXML : $confXML"
::piLog::log [clock milliseconds] "info" "port serverLogs : $port(serverLogs)"
::piLog::log [clock milliseconds] "info" "port serverCultiPi : $port(serverCultiPi)"


proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv - pid [pid] -$message-"
}

# Load server
::piLog::log [clock millisecond] "info" "starting serveur"
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

# Chargement des paramètres de chaque prise
for {set i 1} {$i < $nbPlug} {incr i} {

    set plugXXFilename [file join $confPath plg "plug[string map {" " "0"} [format %2.f $i]]"]
    
    # On vérifie la présence du fichier
    if {[file exists $plugXXFilename] != 1} {
        ::piLog::log [clock milliseconds] "error" "File $plugXXFilename does not exists"
    } else {
        ::piLog::log [clock milliseconds] "info" "reading $i plugXX $plugXXFilename"
        set plug($i,lastValue) "" 
        set fid [open $plugXXFilename r]
        while {[eof $fid] != 1} {
            gets $fid OneLine
            switch [string range $OneLine 0 3] {
                "REG:" {
                    set plug($i,REG,type) [string index $OneLine 4] 
                    set plug($i,REG,sens) [string index $OneLine 5]
                    set plug($i,REG,precision) [expr [string range $OneLine 6 8] / 10.0]
                }
                "SEC:" {
                    set plug($i,SEC,type) [string index $OneLine 4] 
                    set plug($i,SEC,sens) [string index $OneLine 5]
                    set plug($i,SEC,etat_prise) [string index $OneLine 6]
                    set plug($i,SEC,value) [expr [string range $OneLine 7 9] / 10.0]
                }
                "SEN:" {
                    set type  [string index $OneLine 4] 
                    if {$type != "M" && $type != "I" && $type != "A"} {
                        ::piLog::log [clock milliseconds] "error" "Plug $i : type of compute -$type- doesnot exist (replaced by M)"
                        set type "M"
                    }
                    set plug($i,calcul,type) $type
                    set plug($i,calcul,capteur_1) [string index $OneLine 5]
                    set plug($i,calcul,capteur_2) [string index $OneLine 6]
                    set plug($i,calcul,capteur_3) [string index $OneLine 7] 
                    set plug($i,calcul,capteur_4) [string index $OneLine 8]
                    set plug($i,calcul,capteur_5) [string index $OneLine 9]
                    set plug($i,calcul,capteur_6) [string index $OneLine 10]
                }
                "STOL" {
                    set plug($i,SEC,precision) [expr [string range $OneLine 5 7] / 10.0]
                }
                default {
                }
            }
        }
        close $fid
        
        # On affiche les caractéristiques des prises
        ::piLog::log [clock milliseconds] "info" "Plug $i - REG,type: $plug($i,REG,type) - REG,sens: $plug($i,REG,sens) - REG,precision: $plug($i,REG,precision)"
        
    }

}

# initialisation de la partie émetteur
::piLog::log [clock milliseconds] "info" "emeteur_init"
emeteur_init

# Initialisation de la partie lecture capteur
::piLog::log [clock milliseconds] "info" "::sensor::init"
::sensor::init

# Boucle de régulation
::piLog::log [clock milliseconds] "info" "emeteur_update_loop"
emeteur_update_loop

# Boucle de lecture des capteurs
::piLog::log [clock milliseconds] "info" "::sensor::loop"
::sensor::loop

# Une fois la boucle de régulation démarrée , on peut activer le pilotage des prises
::wireless::start

# Pour les client qui ont un abonnement événementiel aux données
emeteur_subscriptionEvenement

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\serverPlugUpdate\serverPlugUpdate.tcl" 6003 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverPlugUpdate\conf.xml" 6001 