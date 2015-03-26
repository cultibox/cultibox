
proc messageGestion {message networkhost} {

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
            ::piServer::sendToServer $serverForResponse "$::port(serverHisto) $indexForResponse pid serverHisto [pid]"
        }
        "getRepere" {
            # La variable est le nom de la variable à lire
            set variable  [::piTools::lindexRobust $message 3]

            ::piLog::log [clock milliseconds] "info" "Asked getRepere $variable"
            # Les parametres d'un repere : nom Valeur 
            
            if {[info exists ::$variable] == 1} {
            
                eval set returnValue $$variable
            
                ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse getRepere $returnValue"
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getRepere $returnValue" $networkhost
            } else {
                ::piLog::log [clock milliseconds] "error" "Asked variable $variable - variable doesnot exists"
            }
        }
        "_getPort" {
            set ::port([::piTools::lindexRobust $message 3]) [::piTools::lindexRobust $message 4]
            ::piLog::log [clock milliseconds] "debug" "getPort response : module [::piTools::lindexRobust $message 3] port [::piTools::lindexRobust $message 4]"
        }
        "_getRepere" {
            # On parse le retour de la commande
            set indexCapteur  [::piTools::lindexRobust $message 3]
            set valeurCapteur [::piTools::lindexRobust $message 4]
            
            # On sauvegarde la valeur du capteur
            set ::sensor(${indexCapteur}) $valeurCapteur
            
            ::piLog::log [clock milliseconds] "debug" "getRepere response : capteur $indexCapteur valeur -$valeurCapteur-"
        }
        "_subscription" -
        "_subscriptionEvenement" {
            # On parse le retour de la commande
            set variable    [::piTools::lindexRobust $message 3]
            set valeur      [::piTools::lindexRobust $message 4]
            set time        [::piTools::lindexRobust $message 5]
            
            # On enregistre le retour de l'abonnement
            set ${variable} $valeur
            
            # On traite immédiatement cette info
            set splitted [split ${variable} "(,)"]
            set variableName [lindex $splitted 0]
            switch $variableName {
                "::sensor" {
                    switch [lindex $splitted 2] {
                        "type" {
                            # Si c'est le type de capteur
                            ::piLog::log [clock milliseconds] "debug" "_subscription response : save sensor type : $message"
                            ::sensorAcq::saveType [lindex $splitted 1] $valeur
                        }
                        "value" {
                            set valeur1      [::piTools::lindexRobust $message 4]
                            set valeur2      [::piTools::lindexRobust $message 5]
                            set time         [::piTools::lindexRobust $message 6]
                            # Si c'est la valeur
                            # ::piLog::log [clock milliseconds] "debug" "_subscription response : save sensor value : $message - [lindex $splitted 1] $valeur1 $valeur2 $time"
                            if {$valeur1 == "DEFCOM"} {
                                ::piLog::log [clock milliseconds] "info" "_subscription response : save sensor value : DEFCOM so not saved - msg : $message"
                            } else {
                                ::sql::AddSensorValue [lindex $splitted 1] $valeur1 $valeur2 $time
                            }
                            
                        } 
                        default {
                            ::piLog::log [clock milliseconds] "error" "_subscription response : not rekognize type [lindex $splitted 2]  - msg : $message"
                        }
                    }
                }
                "::plug" {
                    # Si c'est l'état d'une prise, on enregistre immédiatement
                    ::piLog::log [clock milliseconds] "debug" "_subscription response : save plug [lindex $splitted 1] $valeur time $time - msg : $message"
                    ::sql::addPlugState [lindex $splitted 1] $valeur $time
                }
                default {
                    ::piLog::log [clock milliseconds] "error" "_subscription response : unknow variable name $variableName - msg : $message"
                }
            }

            # ::piLog::log [clock milliseconds] "debug" "subscription response : variable $variable valeur -$valeur-"
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
                            ::piLog::log [clock milliseconds] "error" "Not recognize keyword response -${message}-"
                        }                    
                    }
                    
                } else {
                    ::piLog::log [clock milliseconds] "error" "Not requested response -${message}-"
                }
            
                
            } else {
                ::piLog::log [clock milliseconds] "error" "Received -${message}- but not interpreted"
            }
        }
    }
}