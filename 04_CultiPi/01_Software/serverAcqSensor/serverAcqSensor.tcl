# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverAcqSensor)   [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]

# Global var for regulation
set regul(alarme) 0

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools

# Source extern files
source [file join $rootDir serverAcqSensor src adress_sensor.tcl]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0

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
            # Le repere est le numéro de prise
            set repere [::piTools::lindexRobust $message 3]
            set parametre [::piTools::lindexRobust $message 4]
            ::piLog::log [clock milliseconds] "info" "Asked getRepere $repere - parametre $parametre"
            # Les parametres d'un repere : nom Valeur 
            
            if {[array names ::sensor -exact "$repere,$parametre"] != ""} {
                ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse getRepere $::sensor($repere,$parametre)"
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getRepere $::sensor($repere,$parametre)"
            } else {
                ::piLog::log [clock milliseconds] "error" "Asked getRepere $repere - parametre $parametre not recognize"
            }
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
        
        }
    }
}

# Boucle de lecture des capteurs
proc readSensors {} {

    set sensorTypeList [list SHT DS18B20 WATER_LEVEL PH EC OD ORP]
    
    foreach sensorType $sensorTypeList {
    
        for {set index 1} {$index < 10} {incr index} {
        
            # Si ce capteur peut exister
            if {[array names ::sensor -exact "${sensorType},$index,adress"] != ""} {
            
                # Pour lire la valeur d'un capteur, on s'y prend à deux fois :
                # - On écrit la valeur du registre qu'on veut lire
                # - On lit le registre
                set moduleAdress $::sensor($sensorType,$index,adress)
                set register     $::SENSOR_GENERIC_HP_ADR

                set valueHP ""
                set valueLP ""
                set RC [catch {
                    exec setI2C -y 1 $moduleAdress $::SENSOR_GENERIC_HP_ADR
                    set valueHP [exec getI2C -y 1 $moduleAdress]
                    
                    exec setI2C -y 1 $moduleAdress $::SENSOR_GENERIC_LP_ADR
                    set valueLP [exec getI2C -y 1 $moduleAdress]
                } msg]

                if {$RC != 0} {
                    set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                    set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                    ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (adress module : $moduleAdress - register $register) message:-$msg-"
                } else {
                    set ::sensor($sensorType,$index,value,1) [expr $valueHP * 256 + $valueLP]
                    set ::sensor($sensorType,$index,updateStatus) "OK"
                    set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                    ::piLog::log [clock milliseconds] "debug" "sensor $sensorType index $index (adress module : $moduleAdress - register $register) is updated with value $value"
                }
                
                if {$sensorType == "SHT"} {
                
                    set moduleAdress $::sensor($sensorType,$index,adress)
                    set register     $::SENSOR_GENERIC_HP_ADR

                    set valueHP ""
                    set valueLP ""
                    set RC [catch {
                        exec setI2C -y 1 $moduleAdress $::SENSOR_GENERIC_HP2_ADR
                        set valueHP [exec getI2C -y 1 $moduleAdress]
                        
                        exec setI2C -y 1 $moduleAdress $::SENSOR_GENERIC_LP2_ADR
                        set valueLP [exec getI2C -y 1 $moduleAdress]
                    } msg]

                    if {$RC != 0} {
                        set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                        set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                        ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (adress module : $moduleAdress - register $register) message:-$msg-"
                    } else {
                        set ::sensor($sensorType,$index,value,2) [expr $valueHP * 256 + $valueLP]
                        set ::sensor($sensorType,$index,updateStatus) "OK"
                        set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                        ::piLog::log [clock milliseconds] "debug" "sensor $sensorType index $index (adress module : $moduleAdress - register $register) is updated with value $value"
                    }
                
                }
                
                after 30
            
            }
        
        }
    
    }
    
    # Arrêt du server de log
    after 1000 readSensors
}

readSensors

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\01_Software\serverAcqSensor\serverAcqSensor.tcl" 6005 "D:\DONNEES\GR08565N\Mes documents\cbx\04_CultiPi\02_conf\00_defaultConf\serverAcqSensor\conf.xml" 6001 