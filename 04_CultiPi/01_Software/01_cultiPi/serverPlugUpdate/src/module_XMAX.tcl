
namespace eval ::XMAX {
    variable adresse_module
    variable adresse_I2C
    variable register

    # @0x23 cultibox : 0x46
    set adresse_module(105) 0x23
    set adresse_module(105,PWM) 1
    set adresse_module(106) 0x23
    set adresse_module(106,PWM) 2
    set adresse_module(107) 0x23
    set adresse_module(107,PWM) 3
    set adresse_module(108) 0x23
    set adresse_module(108,PWM) 4

    
    # Définition des registres
    set register(STATUS)    0x00
    set register(PWM_1)     0x01
    set register(PWM_2)     0x02
    set register(PWM_3)     0x03
    set register(PWM_4)     0x04
    
    # Dernière valeur de GPIO
    set register(PWM_1_LAST) 0x00
    set register(PWM_2_LAST) 0x00
    set register(PWM_3_LAST) 0x00
    set register(PWM_4_LAST) 0x00
    

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
        set moduleAdresse $adresse_module($address)
        set PWM_selected  $adresse_module($address,PWM)
    }        
    
    if {$moduleAdresse == "NA"} {
        ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Adress $address does not exists "
        return
    }

    # On sauvegarde l'état de la prise
    ::savePlugSendValue $plugNumber $value
    
    # On met à jour le registre
    switch $value {
        "on" {
            set newValueForPWM 255
            set register(PWM_${PWM_selected}_LAST) 
        }
        "off" {
            set newValueForPWM 0
            set register(PWM_${PWM_selected}_LAST) 0
        }
        default {
            set newValueForPWM $value
            set register(PWM_${PWM_selected}_LAST) $value
        }
    }
    
    # Si c'est la même valeur qu'avant, on n'envoie pas
    if {$newValueForPWM == $register(PWM_${PWM_selected}_LAST)} {
        ::piLog::log [clock milliseconds] "debug" "::XMAX::setValue Output PWM_1 does not send (same as old value)"
        return
    }

    # On pilote le registre de sortie
    set RC [catch {
        exec /usr/local/sbin/i2cset -y 1 $adresse_module $register(PWM_${PWM_selected}) $register(PWM_${PWM_selected}_LAST)
    } msg]
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Module $i does not respond :$msg "
    } else {
        ::piLog::log [clock milliseconds] "debug" "::XMAX::setValue Output PWM_${PWM_selected} to $register(PWM_${PWM_selected}_LAST) OK"
    }

}