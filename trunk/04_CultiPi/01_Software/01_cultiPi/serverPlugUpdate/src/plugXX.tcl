
proc plugXX_load {confPath} {
    set i 1
    while {1} {

        set plugXXFilename [file join $confPath plg "plug[string map {" " "0"} [format %2.f $i]]"]
        
        # On vérifie la présence du fichier
        if {[file exists $plugXXFilename] != 1} {
            ::piLog::log [clock milliseconds] "info" "File $plugXXFilename does not exists, so stop reading plugXX files"
            break;
        } else {
            ::piLog::log [clock milliseconds] "info" "reading $i plugXX $plugXXFilename"
            
            # On initialise les constantes de chaque prise
            set ::plug($i,value) ""
            set ::plug($i,updateStatus) ""
            set ::plug($i,updateStatusComment) ""
            set ::plug($i,source) "plugv"
            set ::plug($i,force,value) ""
            set ::plug($i,force,idAfterProc) ""

            set fid [open $plugXXFilename r]
            while {[eof $fid] != 1} {
                gets $fid OneLine
                switch [string range $OneLine 0 3] {
                    "REG:" {
                        set ::plug($i,REG,type) [string index $OneLine 4] 
                        set ::plug($i,REG,sens) [string index $OneLine 5]
                        set ::plug($i,REG,precision) [expr [string range $OneLine 6 8] / 10.0]
                    }
                    "SEC:" {
                        set ::plug($i,SEC,type) [string index $OneLine 4] 
                        set ::plug($i,SEC,sens) [string index $OneLine 5]
                        set ::plug($i,SEC,etat_prise) [string index $OneLine 6]
                        set ::plug($i,SEC,value) [expr [string range $OneLine 7 9] / 10.0]
                    }
                    "SEN:" {
                        set type  [string index $OneLine 4] 
                        if {$type != "M" && $type != "I" && $type != "A"} {
                            ::piLog::log [clock milliseconds] "error" "Plug $i : type of compute -$type- doesnot exist (replaced by M)"
                            set type "M"
                        }
                        set ::plug($i,calcul,type) $type
                        set ::plug($i,calcul,capteur_1) [string index $OneLine 5]
                        set ::plug($i,calcul,capteur_2) [string index $OneLine 6]
                        set ::plug($i,calcul,capteur_3) [string index $OneLine 7] 
                        set ::plug($i,calcul,capteur_4) [string index $OneLine 8]
                        set ::plug($i,calcul,capteur_5) [string index $OneLine 9]
                        set ::plug($i,calcul,capteur_6) [string index $OneLine 10]
                    }
                    "STOL" {
                        set ::plug($i,SEC,precision) [expr [string range $OneLine 5 7] / 10.0]
                    }
                    default {
                    }
                }
            }
            close $fid
            
            # On affiche les caractéristiques des prises
            ::piLog::log [clock milliseconds] "info" "Plug $i - REG,type: $::plug($i,REG,type) - REG,sens: $::plug($i,REG,sens) - REG,precision: $::plug($i,REG,precision)"
            ::piLog::log [clock milliseconds] "info" "Plug $i - SEC,type: $::plug($i,SEC,type) - SEC,sens: $::plug($i,SEC,sens) - SEC,etat_prise: $::plug($i,SEC,etat_prise) - SEC,value: $::plug($i,SEC,value) - SEC,precision: $::plug($i,SEC,precision)"
            
        }
        
        incr i

    }
}