
namespace eval ::DIMMER {
    variable adresse_module
    variable adresse_I2C
    variable register
    
    # @0x10 cultibox : 0x20
    set adresse_module(90) 0x10
    set adresse_module(90,out) 0
    set adresse_module(91) 0x10
    set adresse_module(91,out) 1
    set adresse_module(92) 0x10
    set adresse_module(92,out) 2
    set adresse_module(93) 0x10
    set adresse_module(93,out) 3
    
    # @0x11 cultibox : 0x22
    set adresse_module(95) 0x11
    set adresse_module(95,out) 0
    set adresse_module(96) 0x11
    set adresse_module(96,out) 1
    set adresse_module(97) 0x11
    set adresse_module(97,out) 2
    set adresse_module(98) 0x11
    set adresse_module(98,out) 3

    # @0x12 cultibox : 0x24
    set adresse_module(100) 0x12
    set adresse_module(100,out) 0
    set adresse_module(101) 0x12
    set adresse_module(101,out) 1
    set adresse_module(102) 0x12
    set adresse_module(102,out) 2
    set adresse_module(103) 0x12
    set adresse_module(103,out) 3

    # Adresse des modules
    set adresse_I2C(0) 0x10
    set adresse_I2C(1) 0x11
    set adresse_I2C(2) 0x12
    
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
    set register(DIMMER_1)  0x10
    set register(DIMMER_2)  0x11
    
    # Dernière valeur de GPIO
    set register(GPIO_LAST) 0x00

}

# Cette proc est utilisée pour initialiser les modules
proc ::DIMMER::init {plugList} {
    variable adresse_module
    variable adresse_I2C
    variable register
    
    # Pour chaque module
    for {set i 0} {$i < 3} {incr i} {
    
        # On définit chaque pin en sortie
        set RC [catch {
            exec /usr/local/sbin/i2cset -y 1 $adresse_I2C($i) $register(IODIR) 0x00
        } msg]
        if {$RC != 0} {
            ::piLog::log [clock milliseconds] "error" "::DIMMER::init Module $i does not respond :$msg "
        } else {
            ::piLog::log [clock milliseconds] "debug" "::DIMMER::init init IODIR to 0x00 OK"
        }
        
        # Les sorties sont à zéro par défaut
    }

}

proc ::DIMMER::setValue {plugNumber value address} {
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
        ::piLog::log [clock milliseconds] "error" "::DIMMER::setValue Adress $address does not exists "
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
        ::piLog::log [clock milliseconds] "error" "::DIMMER::setValue Module $i does not respond :$msg "
    } else {
        ::piLog::log [clock milliseconds] "debug" "::DIMMER::setValue Output GPIO to $register(GPIO_LAST) OK"
    }

}