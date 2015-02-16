# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Read argv
set port(serverAcqSensor)   [lindex $argv 0]
set confXML                 [lindex $argv 1]
set port(serverLogs)        [lindex $argv 2]
set port(serverCultiPi)     [lindex $argv 3]

# Global var for regulation
set regul(alarme) 0

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piLog
package require piServer
package require piTools
package require piXML

# Source extern files
source [file join $rootDir serverAcqSensor src adress_sensor.tcl]
source [file join $rootDir serverAcqSensor src serveurMessage.tcl]

# Initialisation d'un compteur pour les commandes externes envoyées
set TrameIndex 0
set SubscriptionIndex 0

# On initialise les variables globales appelable depuis l'extérieur
set ::sensor(firsReadDone) 0

# On initialise la conf XML
array set configXML {
    verbose     debug
    simulator   off
}

# Chargement de la conf XML
set RC [catch {
array set configXML [::piXML::convertXMLToArray $confXML]
} msg]

# On initialise la connexion avec le server de log
::piLog::openLog $port(serverLogs) "serverAcqSensor" $configXML(verbose)
::piLog::log [clock milliseconds] "info" "starting serverAcqSensor - PID : [pid]"
::piLog::log [clock milliseconds] "info" "port serverAcqSensor : $port(serverAcqSensor)"
::piLog::log [clock milliseconds] "info" "confXML : $confXML"
::piLog::log [clock milliseconds] "info" "port serverLogs : $port(serverLogs)"
::piLog::log [clock milliseconds] "info" "port serverCultiPi : $port(serverCultiPi)"
if {$RC != 0} {
    ::piLog::log [clock milliseconds] "error" "$msg"
}

proc bgerror {message} {
    ::piLog::log [clock milliseconds] error_critic "bgerror in $::argv - pid [pid] -$message-"
}

# Load server
::piLog::log [clock millisecond] "info" "starting serveur"
::piServer::start messageGestion $port(serverAcqSensor)
::piLog::log [clock millisecond] "info" "serveur is started"

proc stopIt {} {
    ::piLog::log [clock milliseconds] "info" "Start stopping serverAcqSensor"
    set ::forever 0
    ::piLog::log [clock milliseconds] "info" "End stopping serverAcqSensor"
    
    # Arrêt du server de log
    ::piLog::closeLog
}

# On charge le simulateur uniquement si c'est définit le fichier XML
if {$configXML(simulator) != "off"} {
    source [file join $rootDir serverAcqSensor src simulator.tcl]
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
        set ::sensor($index,value,1) "" ;# Valeur de la premiere donnée du capteur
        set ::sensor($index,value,2) "" ;# Valeur de la deuxième donnée du capteur
        set ::sensor($index,value) ""   ;# Assemblage des deux valeurs du capteurs
        set ::sensor($index,value,time) ""   ;# Heure de lecture de la donnée
        set ::sensor($index,type) ""    ;# Type du capteur (SHT, DS18B20 ...)
        
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
                    exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SLAVE_REG_MINOR_VERSION
                    set minorVersion [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                    exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SLAVE_REG_MAJOR_VERSION
                    set majorVersion [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                } msg]
                
                if {$RC != 0} {
                    set ::sensor($sensorType,$index,connected) 0
                    set ::sensor($sensorType,$index,majorVersion) ""
                    set ::sensor($sensorType,$index,minorVersion) ""
                    set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                    ::piLog::log [clock milliseconds] "info" "sensor $sensorType,$index (adress $moduleAdress) is not connected ($msg)"
                } else {
                    set ::sensor($sensorType,$index,connected) 1
                    set ::sensor($sensorType,$index,majorVersion) $majorVersion
                    set ::sensor($sensorType,$index,minorVersion) $minorVersion
                    ::piLog::log [clock milliseconds] "info" "sensor $sensorType,$index is connected (Version : ${majorVersion}.${minorVersion}) "
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
                        exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_HP_ADR
                        set valueHP [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                        
                        exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_LP_ADR
                        set valueLP [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                    } msg]

                    if {$RC != 0} {
                        set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                        set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                        set ::sensor($index,value,1) ""
                        ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (adress module : $moduleAdress - register $register) message:-$msg-"
                        
                        # On demande un reboot du logiciel dans ce cas
                        ::piLog::log [clock milliseconds] "error" "Ask software cultipi reboot"
                        ::piServer::sendToServer $::port(serverCultiPi) "$::port(serverAcqSensor) [incr ::TrameIndex] stop"
                        
                    } else {
                        set computedValue [expr ($valueHP * 256 + $valueLP) / 100.0]
                        set ::sensor($sensorType,$index,value,1) $computedValue
                        set ::sensor($sensorType,$index,updateStatus) "OK"
                        set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                        ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index (@ $moduleAdress - reg $register) value 1 : $computedValue (raw $valueHP $valueLP)"
                        
                        # On sauvegarde dans le repère global
                        set ::sensor($index,value,1) $computedValue
                        set ::sensor($index,value) $computedValue
                        set ::sensor($index,type) $sensorType
                        set ::sensor($index,value,time) [clock milliseconds]
                    }
                    
                    if {$sensorType == "SHT"} {
                    
                        set moduleAdress $::sensor($sensorType,$index,adress)
                        set register     $::SENSOR_GENERIC_HP2_ADR

                        set valueHP ""
                        set valueLP ""
                        set RC [catch {
                            exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_HP2_ADR
                            set valueHP [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                            
                            exec /usr/local/sbin/i2cset -y 1 $moduleAdress $::SENSOR_GENERIC_LP2_ADR
                            set valueLP [exec /usr/local/sbin/i2cget -y 1 $moduleAdress]
                        } msg]

                        if {$RC != 0} {
                            set ::sensor($sensorType,$index,updateStatus) "DEFCOM"
                            set ::sensor($sensorType,$index,updateStatusComment) ${msg}
                            set ::sensor($index,value,2) ""
                            ::piLog::log [clock milliseconds] "error" "default when reading valueHP of sensor $sensorType index $index (@ $moduleAdress - reg $register) message:-$msg-"

                            # On demande un reboot du logiciel dans ce cas
                            ::piLog::log [clock milliseconds] "error" "Ask software cultipi reboot"
                            ::piServer::sendToServer $::port(serverCultiPi) "$::port(serverAcqSensor) [incr ::TrameIndex] stop"

                        } else {
                            set computedValue [expr ($valueHP * 256 + $valueLP) / 100.0]
                            set ::sensor($sensorType,$index,value,2) $computedValue
                            set ::sensor($sensorType,$index,updateStatus) "OK"
                            set ::sensor($sensorType,$index,updateStatusComment) [clock milliseconds]
                            ::piLog::log [clock milliseconds] "debug" "sensor $sensorType,$index (@ $moduleAdress - reg $register) value 2 : $computedValue (raw $valueHP $valueLP)"
                            
                            # On sauvegarde dans le repère global
                            set ::sensor($index,value,2) $computedValue
                            set ::sensor($index,value) "$::sensor($index,value,1) $::sensor($index,value,2)"
                            
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
    
    # Une fois l'ensemble lu, on l'indique
    set ::sensor(firsReadDone) 1
    
    # On recherche après 1 seconde
    after 1000 readSensors
}

# On cherche les capteurs connectés
searchSensorsConnected

# On lit la valeur des capteurs
readSensors

vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\serverAcqSensor\serverAcqSensor.tcl" 6005 "C:\cultibox\04_CultiPi\02_conf\01_defaultConf_RPi\serverAcqSensor\conf.xml" 6001 