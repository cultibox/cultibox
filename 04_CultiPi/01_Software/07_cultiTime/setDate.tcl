# Ce script permet de vérifier le statut des horloges
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Source files
set rootDir [file dirname [info script]]
source [file join $rootDir src MCP7940N.tcl]

# Démarrage du RTC
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiTime : Démarrage du RTC"
::MCP7940N::start

# L'heure est correcte, on met à jour le RTC
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiTime : Mise à jour de l'heure"
::MCP7940N::setSeconds [clock seconds]

set rtcHour [::MCP7940N::readSeconds]
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiTime : L'heure du RTC est [clock format $rtcHour -format "%Y %b %d %H:%M:%S"]"

#  tclsh /opt/cultitime/setDate.tcl
