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
# On initialise la conf XML
array set configXML {
    verbose     debug
    pathMySQL   "c:/cultibox/xampp/mysql/bin/mysql.exe"
    logPeriode  60
}

# Chargement de la conf XML
set RC [catch {
array set configXML [::piXML::convertXMLToArray $confXML]
} msg]

# On initialise la connexion avec le server de log
::piLog::openLog $port(serverLogs) "serverHisto" $configXML(verbose)
::piLog::log [clock milliseconds] "info" "starting serverHisto - PID : [pid]"
::piLog::log [clock milliseconds] "info" "port serverHisto : $port(serverHisto)"
::piLog::log [clock milliseconds] "info" "confXML : $confXML"
::piLog::log [clock milliseconds] "info" "port serverLogs : $port(serverLogs)"
::piLog::log [clock milliseconds] "info" "port serverCultiPi : $port(serverCultiPi)"
::piLog::log [clock milliseconds] "info" "verbose : $configXML(verbose)"
if {$RC != 0} {
    ::piLog::log [clock milliseconds] "error" "$msg"
}

proc bgerror {message} {
    ::piLog::log [clock milliseconds] error_critic "bgerror in $::argv - pid [pid] -$message-"
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


# On sort les infos sur le fichier XML
::piLog::log [clock milliseconds] "info" "logperiode : $configXML(logPeriode)"
::piLog::log [clock milliseconds] "info" "pathMySQL  : $configXML(pathMySQL)"

# Le chemin vers l'exe de mySql
::sql::init $configXML(pathMySQL)

# On demande le port du serveur d'acquisition
::sensorAcq::init $configXML(logPeriode)

# On lance la boucle de mise à jour des capteurs
::sensorAcq::loop

# On demande le port du serveur de prise
::plugAcq::init

# On lance la boucle de mise à jour du serveur de prise
::plugAcq::loop

vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\serverHisto\serverHisto.tcl" 6003 "C:\cultibox\04_CultiPi\02_conf\00_defaultConf_Win\serverHisto\conf.xml" 6001