#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piLog 1.0

namespace eval ::piLog {
    variable port ""
    variable channel ""
    variable module ""
    variable traceLevel "debug"
}

proc ::piLog::openLog {portNumber moduleName {level debug}} {
    variable port
    variable channel
    variable module
    variable traceLevel
    
    # On initialise avec les parametres d'entrée de la fonction
    set port $portNumber
    set host localhost
    set module $moduleName
    set traceLevel $level
    
    # Ouverture du socket
    set rc [catch { set channel [socket $host $port] } msg]
    
    # S'il y a une erreur lors de l'ouverture du socket
    if {$rc == 1} {
        puts $msg
        return $msg
    }
    
    return 0
}

proc ::piLog::closeLog {} {
    variable channel
    close $channel
    
    return 0
}

proc ::piLog::log {time traceType trace} {
    variable module
    variable channel
    variable traceLevel
    
    set StringToWrite "<${time}><${module}><${traceType}><${trace}>"
    
    # En fonction du niveau de trace demandé, on envoi ou pas
    set toSend 0
    switch $traceLevel {
        "debug" {
            set toSend 1
        }
        "info" {
            if {$traceType == "debug" || $traceType == "info"} {
                set toSend 1
            }
        }
        "warning" {
            if {$traceType == "debug" || $traceType == "info" || $traceType == "warning" } {
                set toSend 1
            }
        }
        "error" {
            if {$traceType == "debug" || $traceType == "info" || $traceType == "warning"  || $traceType == "error"} {
                set toSend 1
            }
        }
        "error_critic" {
            if {$traceType == "debug" || $traceType == "info" || $traceType == "warning"  || $traceType == "error" || $traceType == "error_critic"} {
                set toSend 1
            }
        }
        default {
            set StringToWrite "<${time}><${module}><error><trace level not good : ${trace}>"
            set toSend 1
        }
    }
   
    
    # If channel is not open, log in local
    if {$channel == ""} {
        set fid [open [file join [file dirname [info script]] log_local.txt ] a+]
        puts $fid $StringToWrite
        close $fid
    } else {
        set rc [catch \
        {
            puts $channel $StringToWrite
            flush $channel
        } msg]
        if {$rc == 1} { return $msg }
    }
    

    return 0
}

# lappend auto_path {D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\lib\tcl}
# package require piLog
# ::piLog::openLog 6000 "moduleTest"
# ::piLog::log [clock milliseconds] "Trace type" "ma trace"
# ::piLog::closeLog 