
namespace eval ::XMAX {
    variable adresse_module
    variable adresse_I2C
    variable register

    # @0x23 cultibox : 0x46
    set adresse_module(105) 0x23
    set adresse_module(105,out) 0
    set adresse_module(106) 0x23
    set adresse_module(106,out) 1
    set adresse_module(107) 0x23
    set adresse_module(107,out) 2
    set adresse_module(108) 0x23
    set adresse_module(108,out) 3

    # Adresse des modules
    set adresse_I2C(0) 0x23
    
    # Définition des registres
    set register(IODIR)     0x00
    set register(IPOL)      0x01
    set register(GPINTEN)   0x02
    set register(DEFVAL)    0x03
    set register(INTCON)    0x04
    set register(IOCON)     0x05
    set register(GPPU)      0x06
    set register(INTF)      0x07
    set register(INTCAP)    0x08
    set register(GPIO)      0x09
    set register(OLAT)      0x0A
    
    # Dernière valeur de GPIO
    set register(GPIO_LAST) 0x00

}

# Cette proc est utilisée pour initialiser les modules
proc ::XMAX::init {} {
    variable adresse_module
    variable adresse_I2C
    variable register
    
    # Pour chaque module
    for {set i 0} {$i < 1} {incr i} {
    
        # On définit chaque pin en sortie
        set RC [catch {
            exec /usr/local/sbin/i2cset -y 1 $adresse_I2C($i) $register(IODIR) 0x00
        } msg]
        if {$RC != 0} {
            ::piLog::log [clock milliseconds] "error" "::XMAX::init Module $i does not respond :$msg "
        } else {
            ::piLog::log [clock milliseconds] "debug" "::XMAX::init init IODIR to 0x00 OK"
        }
        
        # Les sorties sont à zéro par défaut
    }

}

proc ::XMAX::setValue {plugNumber value address} {
    variable adresse_module
    variable adresse_I2C
    variable register

    # On cherche le nom du module cooresspondant
    set moduleAdresse "NA"
    set outputPin "NA"
    # Il faut que la clé existe
    if {[array get adresse_module $address] != ""} {
        set moduleAdresse $adresse_module($address)
        set outputPin     $adresse_module($address,out)
    }        
    
    if {$moduleAdresse == "NA"} {
        ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Adress $address does not exists "
        return
    }

    # On sauvegarde l'état de la prise
    ::savePlugSendValue $plugNumber $value
    
    # On met à jour le registre
    if {$value == "on"} {
        set register(GPIO_LAST) [expr $register(GPIO_LAST) | (1 << $outputPin)] 
    } else {
        set register(GPIO_LAST) [expr $register(GPIO_LAST) & ~(1 << $outputPin)]
    }    
    
    # On pilote le registre de sortie
    set RC [catch {
        exec /usr/local/sbin/i2cset -y 1 $moduleAdresse $register(GPIO) $register(GPIO_LAST)
    } msg]
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "::XMAX::setValue Module $i does not respond :$msg "
    } else {
        ::piLog::log [clock milliseconds] "debug" "::XMAX::setValue Output GPIO to $register(GPIO_LAST) OK"
    }

}