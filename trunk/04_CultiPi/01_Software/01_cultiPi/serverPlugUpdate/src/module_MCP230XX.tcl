
# Module 0
# Init : /usr/local/sbin/i2cset -y 1 0x20 0x00 0x00
# Pilotage pin0 : /usr/local/sbin/i2cset -y 1 0x20 0x09 0x01

namespace eval ::MCP230XX {
    variable adresse_module
    variable adresse_I2C
    variable register
    
    # @0x20 cultibox : 0x40
    set adresse_module(60) 0x20
    set adresse_module(60,out) 0
    set adresse_module(61) 0x20
    set adresse_module(61,out) 1
    set adresse_module(62) 0x20
    set adresse_module(62,out) 2
    set adresse_module(63) 0x20
    set adresse_module(63,out) 3
    set adresse_module(64) 0x20
    set adresse_module(64,out) 4
    set adresse_module(65) 0x20
    set adresse_module(65,out) 5
    set adresse_module(66) 0x20
    set adresse_module(66,out) 6
    set adresse_module(67) 0x20
    set adresse_module(67,out) 7
    
    # @0x21 cultibox : 0x42
    set adresse_module(70) 0x21
    set adresse_module(70,out) 0
    set adresse_module(71) 0x21
    set adresse_module(71,out) 1
    set adresse_module(72) 0x21
    set adresse_module(72,out) 2
    set adresse_module(73) 0x21
    set adresse_module(73,out) 3
    set adresse_module(74) 0x21
    set adresse_module(74,out) 4
    set adresse_module(75) 0x21
    set adresse_module(75,out) 5
    set adresse_module(76) 0x21
    set adresse_module(76,out) 6
    set adresse_module(77) 0x21
    set adresse_module(77,out) 7

    # @0x22 cultibox : 0x44
    set adresse_module(80) 0x22
    set adresse_module(80,out) 0
    set adresse_module(81) 0x22
    set adresse_module(81,out) 1
    set adresse_module(82) 0x22
    set adresse_module(82,out) 2
    set adresse_module(83) 0x22
    set adresse_module(83,out) 3
    set adresse_module(84) 0x22
    set adresse_module(84,out) 4
    set adresse_module(85) 0x22
    set adresse_module(85,out) 5
    set adresse_module(86) 0x22
    set adresse_module(86,out) 6
    set adresse_module(87) 0x22
    set adresse_module(87,out) 7

    # Adresse des modules
    set adresse_I2C(0) 0x20
    set adresse_I2C(1) 0x21
    set adresse_I2C(2) 0x22
    
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
    set register($adresse_I2C(0),GPIO_LAST) 0x00
    set register($adresse_I2C(1),GPIO_LAST) 0x00
    set register($adresse_I2C(2),GPIO_LAST) 0x00

    # Initialisation réalisée
    set register($adresse_I2C(0),init_done) 0
    set register($adresse_I2C(1),init_done) 0
    set register($adresse_I2C(2),init_done) 0
    
}

# Cette proc est utilisée pour initialiser les modules
proc ::MCP230XX::init {plugList} {
    variable adresse_module
    variable register

    # Pour chaque adresse, on cherche le module et on l'initialise
    foreach plug $plugList {
    
        set address $::plug($plug,adress)
    
        # On cherche le nom du module correspondant
        set moduleAdresse "NA"
        set outputPin "NA"
        # Il faut que la clé existe
        if {[array get adresse_module $address] != ""} {
            set moduleAdresse $adresse_module($address)
            set outputPin     $adresse_module($address,out)
        }        
        
        if {$moduleAdresse == "NA"} {
            ::piLog::log [clock milliseconds] "error" "::MCP230XX::init Adress $address does not exists "
            return
        }
        
        # On vérifie que l module est initialisé
        if {$register(${moduleAdresse},init_done) == 0} {
            # On définit chaque pin en sortie
            # /usr/local/sbin/i2cset -y 1 0x20 0x00 0x00
            # lecture de l'état des sorties
            # /usr/local/sbin/i2cget -y 1 0x20 0x00
            set RC [catch {
                exec /usr/local/sbin/i2cset -y 1 $moduleAdresse $register(IODIR) 0x00
            } msg]
            if {$RC != 0} {
                ::piLog::log [clock milliseconds] "error" "::MCP230XX::init Module $moduleAdresse does not respond :$msg "
            } else {
                ::piLog::log [clock milliseconds] "info" "::MCP230XX::init init IODIR to 0x00 OK"
                set register(${moduleAdresse},init_done) 1
            }
        }
    
    }
}

proc ::MCP230XX::setValue {plugNumber value address} {
    variable adresse_module
    variable adresse_I2C
    variable register

    # On cherche le nom du module correspondant
    set moduleAdresse "NA"
    set outputPin "NA"
    # Il faut que la clé existe
    if {[array get adresse_module $address] != ""} {
        set moduleAdresse $adresse_module($address)
        set outputPin     $adresse_module($address,out)
    }        
    
    if {$moduleAdresse == "NA"} {
        ::piLog::log [clock milliseconds] "error" "::MCP230XX::setValue Adress $address does not exists "
        return
    }

    # On sauvegarde l'état de la prise
    ::savePlugSendValue $plugNumber $value
    
    # On met à jour le registre
    if {$value == "on"} {
        set register(${moduleAdresse},GPIO_LAST) [expr $register(${moduleAdresse},GPIO_LAST) | (1 << $outputPin)] 
    } else {
        set register(${moduleAdresse},GPIO_LAST) [expr $register(${moduleAdresse},GPIO_LAST) & ~(1 << $outputPin)]
    }    
    
    # On pilote le registre de sortie
    # /usr/local/sbin/i2cset -y 1 0x20 0x09 0x0F
    # /usr/local/sbin/i2cget -y 1 0x20 0x09
    set RC [catch {
        exec /usr/local/sbin/i2cset -y 1 $moduleAdresse $register(GPIO) $register(${moduleAdresse},GPIO_LAST)
    } msg]
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "::MCP230XX::setValue Module $moduleAdresse does not respond :$msg "
    } else {
        ::piLog::log [clock milliseconds] "info" "::MCP230XX::setValue Output GPIO to $register(${moduleAdresse},GPIO_LAST) OK (output pin $outputPin)"
    }

}
