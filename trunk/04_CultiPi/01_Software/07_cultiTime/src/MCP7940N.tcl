
namespace eval ::MCP7940N {
}


# Cette proc démarre le module RTC
proc ::MCP7940N::start {} {

    # On démarre le comptage de l'heure
    exec i2cset -y1 1 0x6f 0x00 0x80
    
    # On vérifie que l'heure tourne bien en 24h en non pas en 12
    set timeFormat [expr [exec i2cget -y 1 0x6f 0x02 b] & 0x40]
    if {$timeFormat != 0} {
        exec i2cset -y1 1 0x6f 0x02 [expr [exec i2cget -y 1 0x6f 0x02 b] & 0xbF]
    }
    

}

# cette proc lit l'heure du RTC et retourne le nombre de seconde
proc ::MCP7940N::readSeconds {} {

    # Lecture du nombre de secondes (@ 0x00)
    set result [exec i2cget -y 1 0x6f 0x00 b]
    set secondes [expr 0x[string index $result 2] & 0x7][string index $result 3]

    # Lecture du nombre de minute
    set result [exec i2cget -y 1 0x6f 0x01 b]
    set minutes [expr 0x[string index $result 2] & 0x7][string index $result 3]
    
    # Lecture du nombre d'heure
    set result [exec i2cget -y 1 0x6f 0x02 b]
    set heures [expr 0x[string index $result 2] & 0x1][string index $result 3]
    
    # Lecture du jour
    set result [exec i2cget -y 1 0x6f 0x04 b]
    set jour [expr 0x[string index $result 2] & 0x3][string index $result 3]

    # Lecture du nombre de mois
    set result [exec i2cget -y 1 0x6f 0x05 b]
    set mois [expr 0x[string index $result 2] & 0x1][string index $result 3]
    
    # Lecture du nombre d'annee
    set result [exec i2cget -y 1 0x6f 0x06 b]
    set annee [string index $result 2][string index $result 3]

    set time [clock scan "$heures:$minutes:$secondes $jour-$mois-$annee" -format "%H:%M:%S %d-%m-%y"]
    
    return $time

}

# Écriture du nombre de secondes
proc ::MCP7940N::setSeconds {secondes} {

    # On arrête l'horloge de tourner
    exec i2cset -y1 1 0x6f 0x00 0x00
    
    # Écriture du nombre de jour
    set ToWrite [expr 0x[clock format $secondes -format "%y"]]
    set result [exec i2cset -y 1 0x6f 0x06 $ToWrite]
    
    # Écriture du nombre de jour
    set ToWrite [expr 0x[clock format $secondes -format "%m"]]
    set result [exec i2cset -y 1 0x6f 0x05 $ToWrite]
    
    # Écriture du nombre de jour
    set ToWrite [expr 0x[clock format $secondes -format "%e"]]
    set result [exec i2cset -y 1 0x6f 0x04 $ToWrite]
    
    # Écriture du nombre d'heures
    set ToWrite [expr 0x[clock format $secondes -format "%H"] & 0x1f]
    set result [exec i2cset -y 1 0x6f 0x02 $ToWrite]
    
    # Écriture du nombre de minute
    set ToWrite [expr 0x[clock format $secondes -format "%M"]]
    set result [exec i2cset -y 1 0x6f 0x01 $ToWrite]
    
    # Écriture du nombre de secondes et on active l'heure
    set ToWrite [expr 0x[clock format $secondes -format "%S"] | 0x80]
    set result [exec i2cset -y 1 0x6f 0x00 $ToWrite]

}

