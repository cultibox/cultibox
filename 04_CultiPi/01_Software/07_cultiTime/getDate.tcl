# Ce script permet de vérifier le statut des horloges
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Source files
set rootDir [file dirname [info script]]
source [file join $rootDir src MCP7940N.tcl]


set rtcHour [::MCP7940N::readSeconds]
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiTime : L'heure du RTC est [clock format $rtcHour -format "%Y %b %d %H:%M:%S"]"

