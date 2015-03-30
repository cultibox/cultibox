# Le cablage est le suivant :
# Du haut vers le bas
# Pin 1 : GPIO16
# Pin 2 : GPIO20
# Pin 3 : GPIO21
# Pin 4 : GPIO26
# Pin 5 : GPIO19
# Pin 6 : GPIO13
# Pin 7 : GPIO6
# Pin 8 : GPIO5


namespace eval ::direct {
    variable debug 0
    variable pin
    set pin(1,GPIO) 16
    set pin(1,address) 50
    set pin(1,init) 0
    set pin(2,GPIO) 20
    set pin(2,address) 51
    set pin(2,init) 0
    set pin(3,GPIO) 21
    set pin(3,address) 52
    set pin(3,init) 0
    set pin(4,GPIO) 26
    set pin(4,address) 53
    set pin(4,init) 0
    set pin(5,GPIO) 19
    set pin(5,address) 54
    set pin(5,init) 0
    set pin(6,GPIO) 13
    set pin(6,address) 55
    set pin(6,init) 0
    set pin(7,GPIO) 6
    set pin(7,address) 56
    set pin(7,init) 0
    set pin(8,GPIO) 5
    set pin(8,address) 57
    set pin(8,init) 0
}

# Cette procédure permet d'initialiser le module
proc ::direct::init {plugList} {
    variable pin 

    # Pour chaque adresse, on cherche la pin et on l'initialise 
    foreach plug $plugList {
    
        set address $::plug($plug,adress)
    
        set pinNumber "NA"
        set pinIndex 0
        for {set i 1} {$i <= 8} {incr i} {
            if {$pin($i,address) == $address} {
                set pinNumber $pin($i,GPIO)
                set pinIndex $i
                break
            }
        }
        
        if {$pinNumber == "NA"} {
            ::piLog::log [clock milliseconds] "error" "::direct::init Adress $address does not exists "
            return
        }
        
        # S'il elle n'est pas initialisée, on le fait
        if {$pin($pinIndex,init) == 0} {
            initPin $pinIndex
        }
    
    }

}

# Cette proc est utlisée pour initialiser les pins en sortie
proc ::direct::initPin {pinIndex} {
    variable pin

    # On définit la pin en sortie
    set RC [catch {
        exec gpio -g mode $pin($pinIndex,GPIO) out
    } msg]
    if {$RC != 0} {::piLog::log [clock milliseconds] "error" "::direct::initPin Not able to defined pin $pin($pinIndex,GPIO) as out -$msg-"}
    
    # On la met à zéro
    set RC [catch {
        exec gpio -g write $pin($pinIndex,GPIO) 0
    } msg]
    if {$RC != 0} {::piLog::log [clock milliseconds] "error" "::direct::initPin Not able to set pin $pin($pinIndex,GPIO) to 0 -$msg-"}

    set pin($pinIndex,init) 1
    
}

proc ::direct::setValue {plugNumber value address} {
    variable pin 

    # On cherche la pin correspondante
    set pinNumber "NA"
    set pinIndex 0
    for {set i 1} {$i <= 8} {incr i} {
        if {$pin($i,address) == $address} {
            set pinNumber $pin($i,GPIO)
            set pinIndex $i
            break
        }
    }
    
    if {$pinNumber == "NA"} {
        ::piLog::log [clock milliseconds] "error" "::direct::setValue Adress $address does not exists "
        return
    }

    # On sauvegarde l'état de la prise
    ::savePlugSendValue $plugNumber $value
    
    if {$value == "on"} {
        set value 1 
    } else {
        set value 0
    }    
    
    set RC [catch {
        exec gpio -g write $pinNumber $value
    } msg]

    if {$RC != 0} {
        set ::plug($plugNumber,updateStatus) "DEFCOM"
        set ::plug($plugNumber,updateStatusComment) ${msg}
        ::piLog::log [clock milliseconds] "error" "::direct::setValue default when updating value of plug $plugNumber (module : direct - pin $pinNumber) message:-$msg-"
    } else {
        set ::plug($plugNumber,updateStatus) "OK"
        set ::plug($plugNumber,updateStatusComment) [clock milliseconds]
        ::piLog::log [clock milliseconds] "debug" "::direct::setValue plug $plugNumber (module : direct - pin $pinNumber)  is updated with value $value"
    }
}
