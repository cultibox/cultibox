# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverHisto)       [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]
set port(serverCultiPi)     [lindex $argv 3]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools
package require piXML
package require piTime

# Chargement des fichiers externes
source [file join $rootDir serverHisto src plugAcq.tcl]
source [file join $rootDir serverHisto src sensorAcq.tcl]
source [file join $rootDir serverHisto src serveurMessage.tcl]
source [file join $rootDir serverHisto src sql.tcl]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

# Initialisation des variables globales
set logPeriode 300
set pathMySQL  "c:/cultibox/xampp/mysql/bin/mysql.exe"

::piLog::openLog $port(serverLogs) "serverHisto"
::piLog::log [clock milliseconds] "info" "starting serverHisto - PID : [pid]"
::piLog::log [clock milliseconds] "info" "port serverHisto : $port(serverHisto)"
::piLog::log [clock milliseconds] "info" "confXML : $confXML"
::piLog::log [clock milliseconds] "info" "port serverLogs : $port(serverLogs)"
::piLog::log [clock milliseconds] "info" "port serverCultiPi : $port(serverCultiPi)"

proc bgerror {message} {
    ::piLog::log [clock milliseconds] erreur_critique "bgerror in $::argv - pid [pid] -$message-"
}

# Load server
::piLog::log [clock millisecond] "info" "starting serveur"
::piServer::start messageGestion $port(serverHisto)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Start stopping serverHisto"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "End stopping serverHisto"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

# Chargement du path
set confPath [file dirname $confXML]

# Chargement de la conf XML
set XML [lindex [::piXML::open_xml $confXML] 2]

# La fréquence de mise à jour des logs
set logPeriodeItem  [::piXML::searchItemByName logPeriode $XML]
set logPeriode      [::piXML::searchOptionInElement valInSec $logPeriodeItem]
::piLog::log [clock milliseconds] "info" "logperiode : $logPeriode"

# Le chemin vers l'exe de mySql
set pathMySQLItem  [::piXML::searchItemByName pathMySQL $XML]
set pathMySQL      [::piXML::searchOptionInElement path $pathMySQLItem]
::piLog::log [clock milliseconds] "info" "pathMySQL : $pathMySQL"

# On demande le port du serveur d'acquisition
::sensorAcq::init

# On lance la boucle de mise à jour des capteurs
::sensorAcq::loop

# On demande le port du serveur de prise
::plugAcq::init

# On lance la boucle de mise à jour du serveur de prise
::plugAcq::loop

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\01_cultiPi\serverHisto\serverHisto.tcl" 6003 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverHisto\conf.xml" 6001