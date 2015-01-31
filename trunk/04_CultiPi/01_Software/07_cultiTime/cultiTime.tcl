# Ce script permet de v�rifier le statut des horloges
# Il faut que Wiring Pi soit install� : http://wiringpi.com/download-and-install/

# Source files
set rootDir [file dirname [info script]]
source [file join $rootDir src MCP7940N.tcl]

# Le principe est simple :
# Au d�marrage , Si l'heure du RPi n'est pas � jour , on met � jour par rapport � l'horloge temps r�el
# Toutes les jours, on met � jour l'heure du RTC
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : V�rification de l'heure du RPI"

set piHour [clock seconds]
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure du RPi est [clock format $piHour -format "%b %d %H:%M:%S"]"

# Lecture de l'heure du RTC
set rtcHour [::MCP7940N::readSeconds]
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure du RTC est [clock format $rtcHour -format "%b %d %H:%M:%S"]"

# Si l'heure du RPI est ridicule et que l'heure du RTC n'est pas d�connant, on met � jour l'heure du RPi
if {$piHour < 1421942284} {

    # L'heure du RPi est inf�rieur � 16:54:30 22-01-15
    puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure du RPi n'est pas bonne"

    # On v�rifie maintenant que l'heure du RTC n'est pas d�connante
    if {$rtcHour > 1421942284} {
        puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : Mise � jour de l'heure du RPi"
        
        #date MMDDhhmmYY.ss
        exec date [clock format $rtcHour -format "%m%d%H%M%y.%S"]
        
        puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure est mise � jour"

    } else {
        puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure du RTC n'est pas bonne"
    }

}

# Tous les jours, on met � jour l'heure du RTC
proc updateRTCTime {} {

    set piHour [clock seconds]
    if {$piHour > 1421942284} {
    
        puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : Sauvegarde de l'heure dans le RTC"
    
        # L'heure est correcte, on met � jour le RTC
        ::MCP7940N::setSeconds [clock seconds]
        
        # L'heure est correct, on attend 24 heures
        after [expr 100 * 24 * 60 * 60] updateRTCTime
    } else {
    
        puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiTime : L'heure du RPi est incorrecte. On attend une minute avant de sauvegarder l'heure"
    
        # l'heure est incorrect, on attend que 1 minutes
        after [expr 100 * 60] updateRTCTime
    }

    
}

updateRTCTime

# On attend ind�finiment
vwait forever
