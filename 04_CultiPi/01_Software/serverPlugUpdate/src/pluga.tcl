
set SLAVE_EMETOR_ADRESS 0x55 ;# Dans la Cultibox 0xaa
# Les autres modules de pilotage ont les adresses suivantes
set SLAVE_MODULE_1_ADRESS 0x30 ;# Dans la Cultibox 0x60
set SLAVE_MODULE_2_ADRESS 0x31 ;# Dans la Cultibox 0x62
set SLAVE_MODULE_3_ADRESS 0x32 ;# Dans la Cultibox 0x64
set SLAVE_MODULE_4_ADRESS 0x33 ;# Dans la Cultibox 0x66

proc readPluga {plugaFileName} {

    ::piLog::log [clock milliseconds] "info" "pluga filename : $plugaFileName"
    set fid [open $plugaFileName r]
    set nbPlug 0
    while {[eof $fid] != 1} {
        gets $fid OneLine
        if {$OneLine != "" && $nbPlug != 0} {
        
            # Par défaut l'adresse est celle de l'émetteur
            set ::plug($nbPlug,adress) $OneLine
            set ::plug($nbPlug,moduleAdress) $::SLAVE_EMETOR_ADRESS
            
            # Les adresses de 4 à 30 (seulement les nombres pair) sont pour des prises 3500W
            # Les adresses 247	222	219	215	207	252	250	246	238	187	183	189	125	123	119 sont pour les prises 1000W
            # Les adresses >= 256 sont pour les autres modules de pilotages . Les 4 derniers bits (LSB) donnent la prises . 
            # Les 4 d'avant le numéro du module (auquel on ajoute 0x30)
            # Module 1 : Adresse 256 --> 271
            # Module 2 : Adresse 272 --> 287
            if {$OneLine > 255} {
                set ::plug($nbPlug,adress) [expr ($OneLine - 256) % 8]
                set ::plug($nbPlug,moduleAdress) [expr 0x30 + ($OneLine - 256) / 8]
            }
            
        }
        if {$OneLine != "" } {
            incr nbPlug
        }
    }
    close $fid
    
    return $nbPlug

}