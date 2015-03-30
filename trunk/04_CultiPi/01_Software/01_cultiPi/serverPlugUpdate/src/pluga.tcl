
set SLAVE_EMETOR_ADRESS 0x55 ;# Dans la Cultibox 0xaa
# Les autres modules de pilotage ont les adresses suivantes
set SLAVE_MODULE_1_ADRESS 0x30 ;# Dans la Cultibox 0x60
set SLAVE_MODULE_2_ADRESS 0x31 ;# Dans la Cultibox 0x62
set SLAVE_MODULE_3_ADRESS 0x32 ;# Dans la Cultibox 0x64
set SLAVE_MODULE_4_ADRESS 0x33 ;# Dans la Cultibox 0x66

# La liste des modules utilisées
set ::moduleSlaveUsed(info) "Ce vecteur définit les modules utilisés"

proc readPluga {plugaFileName} {

    ::piLog::log [clock milliseconds] "info" "pluga filename : $plugaFileName"
    set fid [open $plugaFileName r]
    set nbPlug 0
    while {[eof $fid] != 1} {
        gets $fid OneLine
        if {$OneLine != "" && $nbPlug != 0} {
        
            # On lit la valeur de l'adresse
            set ::plug($nbPlug,adress) [string trimleft $OneLine "0"]
            
            # On en déduit le module
            set ::plug($nbPlug,module) [::address::get_module $::plug($nbPlug,adress)]

            # On sauvegarde dans le vecteur ce module. Cela permettra de l'initialiser plus tard
            lappend ::moduleSlaveUsed($::plug($nbPlug,module)) $nbPlug

            ::piLog::log [clock milliseconds] "debug" "pluga plug $nbPlug - Address : $::plug($nbPlug,adress) - Module : $::plug($nbPlug,module)"
            
        }
        if {$OneLine != "" } {
            incr nbPlug
        }
    }
    close $fid
    
    set nbPlug [expr $nbPlug - 1]
    
    ::piLog::log [clock milliseconds] "debug" "pluga nbPlug find $nbPlug"
    return $nbPlug

}