# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverAcqSensor)   [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]

set debug 0

# Global var for regulation
set regul(alarme) 0

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

# Source extern files
source [file join $rootDir serverAcqSensor src adress_sensor.tcl]
if {$debug == 1} {
    source [file join $rootDir serverAcqSensor src simulator.tcl]
}

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0
set SubscriptionIndex 0

::piLog::openLog $port(serverLogs) "serverAcqSensor"
::piLog::log [clock milliseconds] "info" "starting serverAcqSensor - PID : [pid]"


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
            ::piServer::sendToServer $serverForResponse "$::port(serverAcqSensor) $indexForResponse pid serverAcqSensor [pid]"
        }
        "getRepere" {
            # Le repère est l'index des capteurs
            set repere [::piTools::lindexRobust $message 3]
            ::piLog::log [clock milliseconds] "info" "Asked getRepere $repere"
            
            # Les parametres d'un repere : nom Valeur 
            if {[array names ::sensor -exact "$repere"] != ""} {
                ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse _getRepere $::sensor($repere)"
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse _getRepere $repere $::sensor($repere)"
            } else {
                ::piLog::log [clock milliseconds] "error" "Asked getRepere $repere  not recognize"
            }
        }
        "subscription" {
            # Le repère est l'index des capteurs
            set repere [::piTools::lindexRobust $message 3]
            set frequency [::piTools::lindexRobust $message 4]
            
            ::piLog::log [clock milliseconds] "info" "Subscription of $repere by $serverForResponse frequency $frequency"
            
            
            set ::subscriptionVariable($::SubscriptionIndex) ""
            
            # On cré la proc associée
            proc subscription${::SubscriptionIndex} {repere frequency SubscriptionIndex serverForResponse} {

                set reponse $::sensor($repere)
                if {$::sensor($repere) == ""} {
                    set reponse "DEFCOM"
                }
            
                # On envoi la nouvelle valeur uniquement si la valeur a changée
                if {$::subscriptionVariable($SubscriptionIndex) != $reponse} {
                    ::piServer::sendToServer $serverForResponse "$serverForResponse [incr ::TrameIndex] _subscription $repere $reponse"
                    set ::subscriptionVariable($SubscriptionIndex) $reponse
                }
                
                after $frequency "subscription${SubscriptionIndex} $repere $frequency $SubscriptionIndex $serverForResponse"
            }
            
            # on la lance
            subscription${::SubscriptionIndex} $repere $frequency $::SubscriptionIndex $serverForResponse
            
            incr ::SubscriptionIndex
        }
        default {
            # Si on reçoit le retour d'une commande, le nom du serveur est le notre
            if {$serverForResponse == $::port(serverAcqSensor)} {
            
                if {[array names ::TrameSended -exact $indexForResponse] != ""} {
                    
                    switch [lindex $::TrameSended($indexForResponse) 0] {
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
::piServer::start messageGestion $port(serverAcqSensor)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Start stopping serverAcqSensor"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "End stopping serverAcqSensor"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

# Initialisation pour tous les capteurs des valeurs
set sensorTypeList [list SHT DS18B20 WATER_LEVEL PH EC OD ORP]

foreach sensorType $sensorTypeList {

    for {set index 1} {$index < 10} {incr index} {
        # Si ce capteur peut exister
        if {[array names ::sensor -exact "${sensorType},$index,adress"] != ""} {
        
            set ::sensor($sensorType,$index,value,1) ""
            set ::sensor($sensorType,$index,value,2) ""
            set ::sensor($sensorType,$index,updateStatus) "INIT"
            set ::sensor($sensorType,$index,commStatus) "DEFCOM"
            set ::sensor($sensorType,$index,majorVersion) ""
            set ::sensor($sensorType,$index,minorVersion) ""
            set ::sensor($sensorType,$index,connected) 0
        }
        
        # On ajoute un repère pour factoriser par numéro de capteur
        set ::sensor($index,value,1) ""
        set ::sensor($index,value,2) ""
        
    }
}

# Boucle pour connaitre les capteurs de connectés
proc searchSensorsConnected {} {
    set sensorTypeList [list SHT DS18B20 WATER_LEVEL PH EC OD ORP]
    
    foreach sensorType $sensorTypeList {
    
        for {set index 1} {$index < 10} {incr index} {
        
            # Si ce capteur peut exister
            if {[array names ::sensor -exact "${sensorType},$index,adress"] != ""} {
            
                set minorVersion ""
                set majorVersion ""
                set moduleAdress $::sensor(${sensorType},$index,adress)
            
                set RC [catch {
                    exec i2cset -y 1 $moduleAdress $::SLAVE_REG_MINOR_VERSION
                    set minorVersion [exec i2cget -y 1 $moduleAdress]
                    exec i2cset -y 1 $moduleAdress $::SLAVE_REG_MAJOR_VERSION
                    set majorVersion [exec i2cget -y 1 $moduleAdress]
                } msg]
                
                if {$RC != 0} {
                    set ::sensor($sensorType,$index,connected) 0
                    set ::sensor($sensorType,$index,majorVersion) ""
                    set ::sensor($sensorType,$index,minorVersion) ""
                    set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                    ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index (adress $moduleAdress) is not connected ($msg)"
                } else {
                    set ::sensor($sensorType,$index,connected) 1
                    set ::sensor($sensorType,$index,majorVersion) $majorVersion
                    set ::sensor($sensorType,$index,minorVersion) $minorVersion
                    ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index is connected (Version : ${majorVersion}.${minorVersion}) "
                }
            }
        }
    }    
}

# Boucle de lecture des capteurs
set indexForSearchingSensor 0
proc readSensors {} {

    set sensorTypeList [list SHT DS18B20 WATER_LEVEL PH EC OD ORP]
    
    foreach sensorType $sensorTypeList {
    
        for {set index 1} {$index < 10} {incr index} {
        
            # Si ce capteur peut exister
            if {[array names ::sensor -exact "${sensorType},$index,adress"] != ""} {
            
                # On vérifie s'il est connecté
                if {$::sensor($sensorType,$index,connected) == 1} {
                
                    # Pour lire la valeur d'un capteur, on s'y prend à deux fois :
                    # - On écrit la valeur du registre qu'on veut lire
                    # - On lit le registre
                    set moduleAdress $::sensor($sensorType,$index,adress)
                    set register     $::SENSOR_GENERIC_HP_ADR

                    set valueHP ""
                    set valueLP ""
                    set RC [catch {
                        exec i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_HP_ADR
                        set valueHP [exec i2cget -y 1 $moduleAdress]
                        
                        exec i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_LP_ADR
                        set valueLP [exec i2cget -y 1 $moduleAdress]
                    } msg]

                    if {$RC != 0} {
                        set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                        set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                        set ::sensor($index,value,1) ""
                        ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (adress module : $moduleAdress - register $register) message:-$msg-"
                    } else {
                        set computedValue [expr ($valueHP * 256 + $valueLP) / 100.0]
                        set ::sensor($sensorType,$index,value,1) $computedValue
                        set ::sensor($sensorType,$index,updateStatus) "OK"
                        set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                        ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index (@ $moduleAdress - reg $register) value 1 : $computedValue"
                        
                        # On sauvegarde dans le repère global
                        set ::sensor($index,value,1) $computedValue
                    }
                    
                    if {$sensorType == "SHT"} {
                    
                        set moduleAdress $::sensor($sensorType,$index,adress)
                        set register     $::SENSOR_GENERIC_HP_ADR

                        set valueHP ""
                        set valueLP ""
                        set RC [catch {
                            exec i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_HP2_ADR
                            set valueHP [exec i2cget -y 1 $moduleAdress]
                            
                            exec i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_LP2_ADR
                            set valueLP [exec i2cget -y 1 $moduleAdress]
                        } msg]

                        if {$RC != 0} {
                            set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                            set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                            set ::sensor($index,value,2) ""
                            ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (@ $moduleAdress - reg $register) message:-$msg-"
                        } else {
                            set computedValue [expr ($valueHP * 256 + $valueLP) / 100.0]
                            set ::sensor($sensorType,$index,value,2) $computedValue
                            set ::sensor($sensorType,$index,updateStatus) "OK"
                            set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                            ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index (@ $moduleAdress - reg $register) value 2 : $computedValue"
                            
                            # On sauvegarde dans le repère global
                            set ::sensor($index,value,2) $computedValue
                            
                        }
                    
                    }
                }            
            }
        
        }
    
    }
    
    if {[incr ::indexForSearchingSensor] > 60} {
        # Une fois sur 60 , on recherche les capteurs
        set ::indexForSearchingSensor 0
        searchSensorsConnected
    }
    
    # On rechercher après 1 seconde
    after 1000 readSensors
}

# On cherche les capteurs connectés
searchSensorsConnected

# On lit la valeur des capteurs
readSensors

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\serverAcqSensor\serverAcqSensor.tcl" 6005 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverAcqSensor\conf.xml" 6001 