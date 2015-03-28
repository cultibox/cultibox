# Ce script permet de v�rifier le statut des horloges
# Il faut que Wiring Pi soit install� : http://wiringpi.com/download-and-install/

# Source files
set rootDir [file dirname [info script]]
source [file join $rootDir src MCP7940N.tcl]

# D�marrage du RTC
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiTime : D�marrage du RTC"
::MCP7940N::start

# L'heure est correcte, on met � jour le RTC
::MCP7940N::setSeconds [clock seconds]
        