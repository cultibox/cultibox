# Ce fichier définit pour chaque adresse le module à appeler

namespace eval ::address {
    variable val
    
    # @0x55 , cultibox : 0xaa
    set val(4) wireless
    set val(6) wireless
    set val(8) wireless
    set val(10) wireless
    set val(12) wireless
    set val(14) wireless
    set val(16) wireless
    set val(18) wireless
    set val(20) wireless
    set val(22) wireless
    set val(24) wireless
    set val(26) wireless
    set val(28) wireless
    set val(30) wireless
    set val(247) wireless
    set val(222) wireless
    set val(219) wireless
    set val(215) wireless
    set val(207) wireless
    set val(252) wireless
    set val(250) wireless
    set val(246) wireless
    set val(238) wireless
    set val(187) wireless
    set val(183) wireless
    set val(189) wireless
    set val(125) wireless
    set val(123) wireless
    set val(119) wireless

    # Adresses pour la commande directe
    set val(50) direct
    set val(51) direct
    set val(52) direct
    set val(53) direct
    set val(54) direct
    set val(55) direct
    set val(56) direct
    set val(57) direct
    
    # Adresse pour la commande en utilisant MCP23008 (optionnel-ment MCP23017)
    # @0x20 cultibox : 0x40
    set val(60) MCP230XX
    set val(61) MCP230XX
    set val(62) MCP230XX
    set val(63) MCP230XX
    set val(64) MCP230XX
    set val(65) MCP230XX
    set val(66) MCP230XX
    set val(67) MCP230XX
    
    # @0x21 cultibox : 0x42
    set val(70) MCP230XX
    set val(71) MCP230XX
    set val(72) MCP230XX
    set val(73) MCP230XX
    set val(74) MCP230XX
    set val(75) MCP230XX
    set val(76) MCP230XX
    set val(77) MCP230XX
    
    # @0x22 cultibox : 0x44
    set val(80) MCP230XX
    set val(81) MCP230XX
    set val(82) MCP230XX
    set val(83) MCP230XX
    set val(84) MCP230XX
    set val(85) MCP230XX
    set val(86) MCP230XX
    set val(87) MCP230XX
    
    # Adresse pour la commande en utilisant le vario
    # @0x10 cultibox : 0x20
    set val(90) DIMMER
    set val(91) DIMMER
    set val(92) DIMMER
    set val(93) DIMMER
    
    # @0x11 cultibox : 0x22
    set val(95) DIMMER
    set val(96) DIMMER
    set val(97) DIMMER
    set val(98) DIMMER

    # @0x12 cultibox : 0x24
    set val(100) DIMMER
    set val(101) DIMMER
    set val(102) DIMMER
    set val(103) DIMMER
    
    # Adresse pour XMAX
    # @0x23 cultibox : 0x46
    set val(105) XMAX
    set val(106) XMAX
    set val(107) XMAX
    set val(108) XMAX
    
    # Adresse pour d'autre cultipi (10 modules)
    # @1000 --> 1176
    for {set j 0} {$j < 10} {incr j} {
        for {set i 0} {$i < 16} {incr i} {
            set val([expr 1000 + 16 * $j + $i]) CULTIPI
        }
    }
    
    
}

proc ::address::get_module {address} {
    variable val

    if {[array get val $address] != ""} {
        return $val($address)
    } else {
        return "NA"
    }
    

}