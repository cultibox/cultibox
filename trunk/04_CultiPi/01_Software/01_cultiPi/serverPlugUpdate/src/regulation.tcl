

proc emeteur_regulation {nbPlug plgPrgm} {

    set programmeToSend $::actualProgramm
    
    # On vérifie si l'état de la dernière commande envoyée existe
    if {[array name ::plug -exact $nbPlug,lastValue] == ""} {
        set ::plug($nbPlug,lastValue) ""
    }
    
    if {$plgPrgm == ""} {
        ::piLog::log [clock milliseconds] "error" "Plug $nbPlug programme is empty"
    } elseif {$plgPrgm != "off" && $plgPrgm != "on"} {

        # En fonction de la conf la prise doit être allumée ou éteinte en régulation secondaire
        set etatSecondaire "off"
        if {$::plug($nbPlug,SEC,etat_prise) == "1"} {
            set etatSecondaire "on"
        }
        
        set valeurToPilot ""
        
        # On vérifie d'abord si la régulations secondaire doit être activée
        if {$::plug($nbPlug,SEC,type) != "N"} {
        
            # Le calcul de la régulation du secondaire est toujours réalisée sur la moyenne
            set valueSecondaire [computeValueForRegulation $nbPlug $::plug($nbPlug,SEC,type) "M"]
            set consigneSupSec [expr $plgPrgm + $::plug($nbPlug,SEC,precision)]
            set consigneInfSec [expr $plgPrgm - $::plug($nbPlug,SEC,precision)]
        
            # Si le sens de la régulation est +
            if {$::plug($nbPlug,SEC,sens) == "+"} {
                # Si la valeur du capteur est supérieur à la consigne
                if {$valueSecondaire > $consigneSupSec} {
                
                    # On force la prise dans l'état défini dans la conf
                    set valeurToPilot $etatSecondaire
                    
                    ::piLog::log [clock milliseconds] "debug" "regSec+ Sup plug:-$nbPlug- progr:-$plgPrgm- value:-$valueSecondaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupSec- trigLow:-$consigneInfSec-"
                    
                } elseif {$valueSecondaire > $consigneInfSec  && $::plug($nbPlug,lastValue) == "on"} {
                
                    # Sensor is not upper than consigne but between two marges, keep the last consigne
                    set valeurToPilot $::plug($nbPlug,lastValue)
                    
                    ::piLog::log [clock milliseconds] "debug" "regSec+ Between plug:-$nbPlug- progr:-$plgPrgm- value:-$valueSecondaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupSec- trigLow:-$consigneInfSec-"
                    
                }
            } else {
                # Sinon le sens de la régulation est "-"
                if {$valueSecondaire < $consigneInfSec}  {
                
                    # On force la prise dans l'état défini dans la conf
                    set valeurToPilot $etatSecondaire

                    ::piLog::log [clock milliseconds] "debug" "regSec- Inf plug:-$nbPlug- progr:-$plgPrgm- value:-$valueSecondaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupSec- trigLow:-$consigneInfSec-"

                } elseif {$valueSecondaire < $consigneSupSec && $::plug($nbPlug,lastValue) == "on"} {
                
                    # Sensor is not upper than consigne but between two marges, keep the last consigne
                    # Keep the last consigne only if it was ON
                    set valeurToPilot $::plug($nbPlug,lastValue)

                    ::piLog::log [clock milliseconds] "debug" "regSec- Between plug:-$nbPlug- progr:-$plgPrgm- value:-$valueSecondaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupSec- trigLow:-$consigneInfSec-"

                }
            }
        
        }
        
        # Si la régulation secondaire n'a pas définie de valeur, on applique la régulation primaire
        if {$valeurToPilot == ""} {
        
            set valuePrimaire [computeValueForRegulation $nbPlug $::plug($nbPlug,REG,type) $::plug($nbPlug,calcul,type)]
            set consigneSupPri [expr $plgPrgm + $::plug($nbPlug,REG,precision)]
            set consigneInfPri [expr $plgPrgm - $::plug($nbPlug,REG,precision)]
        
            # Search sens
            # If sens is +, effecteur will be on if temp is upper than consigne
            # ie: ventilator, dehumidificator
            if {$::plug($nbPlug,REG,sens) == "+"} {
            
                # Pas de donnée des capteurs
                if {$valuePrimaire == ""} {
                    
                    # Par defaut, si on a pas de valeur, on coupe l'effecteur
                    set valeurToPilot "off"
                    
                    # Si c'est un ventilateur, on le met en route
                    if {$::plug($nbPlug,REG,type) == "T"} {
                        set valeurToPilot "on"
                    }

                    ::piLog::log [clock milliseconds] "debug" "regPri+ NoSensor plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                } elseif {$::plug($nbPlug,moduleType) == "dimmer"} {
                    # Dimmer case
                    # $valeurToPilot < 0
                    # ie : 0100 < 2800 -  2600
                    # ie $valeurToPilot = -200 = 0100 + ( 2600 - 2800)
                    # if {(int)emeteur_regulation_previous_value[uc8_plug] < ((int)emeteur_regulation_value[uc8_plug] - ($valuePrimaire))} {
                    #     set valeurToPilot 0
                    # } else {
                    #     set valeurToPilot (int)emeteur_regulation_previous_value[uc8_plug] + (($valuePrimaire - (int)emeteur_regulation_value[uc8_plug]));
                    # }

                    # if {$valeurToPilot > 10000} {
                    #     set valeurToPilot 10000;
                    # }

                    ::piLog::log [clock milliseconds] "debug" "regPri+ Dimmer plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                } elseif {$::plug($nbPlug,moduleType) == "wirelessplug"} {
                    # Cas de la prise sans fils
                    if {$valuePrimaire > $consigneSupPri} {
                    
                        # Standard plug case
                        set valeurToPilot "on"

                        ::piLog::log [clock milliseconds] "debug" "regPri+ Wireless Sup plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                    } elseif {$valuePrimaire < $consigneInfPri} {
                    
                        set valeurToPilot "off"

                        ::piLog::log [clock milliseconds] "debug" "regPri+ Wireless Inf plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                    }

                }
            } else {
                # sens is -
                if {$valuePrimaire == ""} {
                    # Si pas de donnée capteur, on éteint l'effecteur
                    set valeurToPilot "off"

                    ::piLog::log [clock milliseconds] "debug" "regPri- NoSensor plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                } elseif {$::plug($nbPlug,moduleType) == "dimmer"} {
                    # Dimmer case
                    # If  $valeurToPilot < 0
                    # if {(int)emeteur_regulation_previous_value[uc8_plug] < (($valuePrimaire) - (int)emeteur_regulation_value[uc8_plug])} {
                    #     set valeurToPilot 0
                    # } else {
                    #     set valeurToPilot (int)emeteur_regulation_previous_value[uc8_plug] - (($valuePrimaire - (int)emeteur_regulation_value[uc8_plug]))
                    # }

                    # if {$valeurToPilot > 10000} {
                    #     set valeurToPilot 10000
                    # }

                    ::piLog::log [clock milliseconds] "debug" "regPri- Dimmer plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                } elseif {$::plug($nbPlug,moduleType) == "wirelessplug"} {
                
                    # Cas de la prise sans fils
                    if {$valuePrimaire < $consigneInfPri} {
                    
                        # Standard plug case
                        set valeurToPilot "on"

                        ::piLog::log [clock milliseconds] "debug" "regPri- Wireless Inf plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                    } elseif {$valuePrimaire > $consigneSupPri} {
                    
                        set valeurToPilot "off"

                        ::piLog::log [clock milliseconds] "debug" "regPri- Wireless Sup plug:-$nbPlug- progr:-$plgPrgm- value:-$valuePrimaire- pilot:-$valeurToPilot- trigHigh:-$consigneSupPri- trigLow:-$consigneInfPri-"

                    }
                }
            }
        }
        
        # On envoi la commande au module
        if {$valeurToPilot != "" && $valeurToPilot != $::plug($nbPlug,lastValue)} {
            ::wireless::setValue $nbPlug $valeurToPilot
            set ::plug($nbPlug,lastValue) $valeurToPilot
        }
 
 
    } else {
        ::piLog::log [clock milliseconds] "error" "couldnt make regulation with programm $plgPrgm"
    }

}


proc computeValueForRegulation {nbPlug sensorType computeType} {
        
    # Calcul de la valeur pour régulation primaire
    set find 0
    set outValue 0
    
    # On regarde quelle valeur on doit prendre
    set indexSensorValue 1 
    if {$sensorType == "H" } {
        set indexSensorValue 2
    } elseif {$sensorType != "T" } {
        ::piLog::log [clock milliseconds] "error" "computeValueForRegulation : sensortype $sensorType is not recognize"
    }

    switch $computeType {
        "M" {
            set nbValue 0
            for {set i 1} {$i < 7} {incr i} {
                set valeurCapteur $::sensor(${i},value,${indexSensorValue})
                if {$::plug($nbPlug,calcul,capteur_$i) != 0 && $valeurCapteur != "DEFCOM" && $valeurCapteur != ""} {
                    set outValue [expr $outValue + $valeurCapteur]
                    set find 1
                    incr nbValue
                }
            }
            if {$nbValue != 0} {
                set outValue [expr $outValue / (1.0 * $nbValue)]
            }
        }
        "I" {
            set outValue ""
            set nbValue 0
            for {set i 1} {$i < 7} {incr i} {
                set valeurCapteur $::sensor(${i},value,${indexSensorValue})
                if {$::plug($nbPlug,calcul,capteur_$i) != 0 && $valeurCapteur != "DEFCOM" && $valeurCapteur != ""} {
                    if {$outValue == "" || $outValue > $valeurCapteur} {
                        set outValue $valeurCapteur
                        set find 1
                    }
                }
            }
        }
        "A" {
            set outValue ""
            set nbValue 0
            for {set i 1} {$i < 7} {incr i} {
                set valeurCapteur $::sensor(${i},value,${indexSensorValue})
                if {$::plug($nbPlug,calcul,capteur_$i) != 0 && $valeurCapteur != "DEFCOM" && $valeurCapteur != ""} {
                    if {$outValue == "" || $outValue < $valeurCapteur} {
                        set outValue $valeurCapteur
                        set find 1
                    }
                }
            }
        }
    }
    
    if {$find == 0} {
        set outValue ""
    }
    
    return $outValue
}