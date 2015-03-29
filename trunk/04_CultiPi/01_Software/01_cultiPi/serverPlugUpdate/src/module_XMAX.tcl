
namespace eval ::XMAX {
    variable adresse_module
    variable register

    # @0x23 cultibox : 0x46
    set adresse_module(105) 0x23

    # Définition des registres
    set register(STATUS)    0x00
    set register(PWM_1)     0x01
    set register(PWM_2)     0x03
    set register(PWM_3)     0x04
    set register(PWM_4)     0x02
    set register(PWM_FAN)   0x05
    set register(TARGET_TEMP) 0x06
    set register(TEMP)  0x07
    
    # Dernière valeur de GPIO
    set register(PWM_1_LAST) 0x00
    set register(PWM_2_LAST) 0x00
    set register(PWM_3_LAST) 0x00
    set register(PWM_4_LAST) 0x00
    set register(TEMP_LAST)  0x00

}

# Cette proc est utilisée pour initialiser les modules
proc ::XMAX::init {} {
    variable adresse_module
    variable register

}

proc ::XMAX::setValue {plugNumber value address} {
    variable adresse_module
    variable register

    # On cherche le nom du module correspondant
    set moduleAdresse "NA"
    set PWM_selected  "NA"
    # Il faut que la clé existe
    if {[array get adresse_module $address] != ""} {
        set I2Cadress $adresse_module($address)
    }        
    
    if {$I2Cadress == "NA"} {
        ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Adress $address does not exists "
        return
    }

    # On sauvegarde l'état de la prise
    ::savePlugSendValue $plugNumber $value
    
    # On met à jour les registres
    switch $value {
        "on" {
            set newValueForPWM(1) 255
            set newValueForPWM(2) 255
            set newValueForPWM(3) 255
        }
        "off" {
            set newValueForPWM(1) 0
            set newValueForPWM(2) 0
            set newValueForPWM(3) 0
        }
        default {
            # On split la valeur a envoyer
            # La valeur ressemble à ça 35.6
            set value [expr round($value * 10)]

            set newValueForPWM(3) [string index $value end]
            if {$newValueForPWM(3) == ""} {
                set newValueForPWM(3) 0
            } elseif {$newValueForPWM(3) == 9} {
                set newValueForPWM(3) 255
            } else {
                set newValueForPWM(3) [expr $newValueForPWM(3) * 28]
            }
            
            set newValueForPWM(2) [string index $value end-1]
            if {$newValueForPWM(2) == ""} {
                set newValueForPWM(2) 0
            } elseif {$newValueForPWM(2) == 9} {
                set newValueForPWM(2) 255
            } else {
                set newValueForPWM(2) [expr $newValueForPWM(2) * 28]
            }
            
            set newValueForPWM(1) [string index $value end-2]
            if {$newValueForPWM(1) == ""} {
                set newValueForPWM(1) 0
            } elseif {$newValueForPWM(1) == 9} {
                set newValueForPWM(1) 255
            } else {
                set newValueForPWM(1) [expr $newValueForPWM(1) * 28]
            }
            
        }
    }
    
    for {set i 1} {$i < 4} {incr i} {
        # Si c'est la même valeur qu'avant, on n'envoie pas
        # if {$newValueForPWM($i) == $register(PWM_${i}_LAST)} {
        #    ::piLog::log [clock milliseconds] "debug" "::XMAX::setValue Output PWM_${i} does not send (same as old value ($newValueForPWM($i)))"
        #} else {
            # Exemple
            # gpio -g write 18 0 ; gpio -g write 18 1
            # /usr/local/sbin/i2cset -y 1 0x23 1 0
            #  /usr/local/sbin/i2cset -y 1 0x23 12 125
            # 1 multicolor
            # 3 Rouge
            # 4 Bleu
            
            # On pilote le registre de sortie
            set RC [catch {
                exec /usr/local/sbin/i2cset -y 1 $I2Cadress $register(PWM_${i}) $newValueForPWM(${i})
            } msg]
            if {$RC != 0} {
                ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Module ${i} with value $newValueForPWM(${i}) does not respond :$msg "
                
                # On lui demande de redémarrer
                ::piServer::sendToServer $::port(serverCultiPi) "$::port(serverAcqSensor) [incr ::TrameIndex] restartSlave"
            } else {
                # On debug !
                ::piLog::log [clock milliseconds] "info" "::XMAX::setValue Output PWM_${i} to $newValueForPWM(${i}) OK"

                # on enregistre
                set register(PWM_${i}_LAST) $newValueForPWM(${i})
            }
        #}
        after 10
    }

}