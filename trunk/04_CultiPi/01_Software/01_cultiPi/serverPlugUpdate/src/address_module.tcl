# Ce fichier définit pour chaque adresse le module à appeler

namespace eval ::address {
    variable val
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
    
}

proc ::address::get_module {address} {
    variable val

    if {[array get val $address] != ""} {
        return $val($address)
    } else {
        return "NA"
    }
    

}