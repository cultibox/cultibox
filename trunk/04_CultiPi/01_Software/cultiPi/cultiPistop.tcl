#!/usr/bin/tclsh

# Init directory
set rootDir [file dirname [file dirname [info script]]]
set logFile [file join $rootDir log.txt]

puts "Stoping cultiPi"
set rc [catch { set channel [socket localhost 6000] } msg]

proc send:data {channel data} \
{
    set rc [catch \
    {
        puts $channel $data
        flush $channel
    } msg]
    if {$rc == 1} { log $msg }
}

# Demande arrêt du server
# Trame standard : [FROM] [INDEX] [commande] [argument]
send:data $channel "NA 0 stop"

# fermeture connexion
close $channel

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"