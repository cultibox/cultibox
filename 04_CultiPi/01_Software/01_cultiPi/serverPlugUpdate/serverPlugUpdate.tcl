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
package require piTime
package require piXML

# Chargement des fichiers externes
source [file join $rootDir serverPlugUpdate src emeteur.tcl]
source [file join $rootDir serverPlugUpdate src pluga.tcl]
source [file join $rootDir serverPlugUpdate src plugXX.tcl]
source [file join $rootDir serverPlugUpdate src sensor.tcl]
source [file join $rootDir serverPlugUpdate src serveurMessage.tcl]
source [file join $rootDir serverPlugUpdate src regulation.tcl]
source [file join $rootDir serverPlugUpdate src forcePlug.tcl]
source [file join $rootDir serverPlugUpdate src address_module.tcl]

# Chargement des différents modules de pilotage
source [file join $rootDir serverPlugUpdate src module_direct.tcl]
source [file join $rootDir serverPlugUpdate src module_wireless.tcl]
source [file join $rootDir serverPlugUpdate src module_CULTIPI.tcl]
source [file join $rootDir serverPlugUpdate src module_DIMMER.tcl]
source [file join $rootDir serverPlugUpdate src module_MCP230XX.tcl]
source [file join $rootDir serverPlugUpdate src module_XMAX.tcl]


# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

# On initialise la conf XML
array set configXML {
    verbose     debug
}

# Chargement de la conf XML
set RC [catch {
    array set configXML [::piXML::convertXMLToArray $confXML]
} msg]
if {$RC != 0} {
    ::piLog::log [clock milliseconds] "error" "$msg"
}

# On initialise la connexion avec le server de log
::piLog::openLog $port(serverLogs) "serverPlugUpdate" $configXML(verbose)

::piLog::log [clock milliseconds] "info" "starting serverPlugUpdate - PID : [pid]"
::piLog::log [clock milliseconds] "info" "port serverPlugUpdate : $port(serverPlugUpdate)"
::piLog::log [clock milliseconds] "info" "confXML : $confXML"
::piLog::log [clock milliseconds] "info" "port serverLogs : $port(serverLogs)"
::piLog::log [clock milliseconds] "info" "port serverCultiPi : $port(serverCultiPi)"
# On affiche les infos dans le fichier de debug
foreach element [array names configXML] {
    ::piLog::log [clock milliseconds] "info" "$element : $configXML($element)"
}


proc bgerror {message} {
    ::piLog::log [clock milliseconds] error_critic "bgerror in $::argv - pid [pid] -$message-"
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


# Parse pluga filename and send adress to module if needed
set EMETEUR_NB_PLUG_MAX [readPluga $plugaFileName]

# Chargement des paramètres de chaque prise
plugXX_load $confPath

# Pour chaque module utilisé, on l'initialise
foreach module [array names ::moduleSlaveUsed] {
    if {$module != "info" && $module != "NA" } {
        ::piLog::log [clock milliseconds] "info" "Module $module is used. So init it"
        ::${module}::init $::moduleSlaveUsed(${module})
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

# Une fois la boucle de régulation démarrée , on peut activer le pilotage des prises (seulement si des prises wireless sont configurées)
if {[array names moduleSlaveUsed -exact wireless] != ""} {
    ::wireless::start
}

# Pour les client qui ont un abonnement événementiel aux données
emeteur_subscriptionEvenement

vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\serverPlugUpdate\serverPlugUpdate.tcl" 6004 "C:\cultibox\04_CultiPi\02_conf\00_defaultConf_Win\serverPlugUpdate\conf.xml" 6003 6000
# tclsh /opt/cultipi/serverPlugUpdate/serverPlugUpdate.tcl 6004 /etc/cultipi/01_defaultConf_RPi/./serverPlugUpdate/conf.xml 6003 6000
